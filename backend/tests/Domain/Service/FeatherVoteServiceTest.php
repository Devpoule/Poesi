<?php

declare(strict_types=1);

namespace App\Tests\Domain\Service;

use App\Domain\Entity\FeatherVote;
use App\Domain\Entity\Poem;
use App\Domain\Entity\User;
use App\Domain\Enum\FeatherType;
use App\Domain\Exception\CannotVote\CannotVoteOwnPoemException;
use App\Domain\Exception\NotFound\FeatherVoteNotFoundException;
use App\Domain\Exception\NotFound\PoemNotFoundException;
use App\Domain\Exception\NotFound\UserNotFoundException;
use App\Domain\Repository\FeatherVoteRepositoryInterface;
use App\Domain\Repository\PoemRepositoryInterface;
use App\Domain\Repository\UserRepositoryInterface;
use App\Domain\Service\FeatherVoteService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FeatherVoteServiceTest extends TestCase
{
    private FeatherVoteRepositoryInterface&MockObject $featherVoteRepository;
    private UserRepositoryInterface&MockObject $userRepository;
    private PoemRepositoryInterface&MockObject $poemRepository;
    private FeatherVoteService $service;

    protected function setUp(): void
    {
        $this->featherVoteRepository = $this->createMock(FeatherVoteRepositoryInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->poemRepository = $this->createMock(PoemRepositoryInterface::class);
        $this->service = new FeatherVoteService(
            $this->featherVoteRepository,
            $this->userRepository,
            $this->poemRepository
        );
    }

    /**
     * 🎯 create a new vote when none exists yet.
     */
    public function test_cast_vote_creates_new_vote(): void
    {
        ## --------| Arrange |-------- ##
        $voter = (new User())
            ->setEmail('voter@test.local')
            ->setPassword('hash')
            ->setRoles(['ROLE_USER']);
        $this->setId($voter, 10);

        $author = (new User())
            ->setEmail('author@test.local')
            ->setPassword('hash')
            ->setRoles(['ROLE_USER']);
        $this->setId($author, 11);

        $poem = (new Poem())
            ->setAuthor($author)
            ->setTitle('Titre')
            ->setContent('Texte');
        $this->setId($poem, 5);

        $captured = null;

        $this->userRepository->method('getById')->with(10)->willReturn($voter);
        $this->poemRepository->method('getById')->with(5)->willReturn($poem);
        $this->featherVoteRepository->method('findOneByVoterAndPoem')->willReturn(null);
        $this->featherVoteRepository
            ->expects($this->once())
            ->method('save')
            ->willReturnCallback(static function (FeatherVote $vote) use (&$captured): void {
                $captured = $vote;
            });

        ## --------| Act |-------- ##
        $result = $this->service->castVote(10, 5, FeatherType::GOLD);

        ## --------| Assert |-------- ##
        $this->assertTrue($result['created']);
        $this->assertInstanceOf(FeatherVote::class, $result['vote']);
        $this->assertSame($captured, $result['vote']);
        $this->assertSame($voter, $result['vote']->getVoter());
        $this->assertSame($poem, $result['vote']->getPoem());
        $this->assertSame(FeatherType::GOLD, $result['vote']->getFeatherType());
    }

    /**
     * 🎯 update an existing vote instead of duplicating.
     */
    public function test_cast_vote_updates_existing(): void
    {
        ## --------| Arrange |-------- ##
        $voter = (new User())
            ->setEmail('voter@test.local')
            ->setPassword('hash')
            ->setRoles(['ROLE_USER']);
        $this->setId($voter, 20);

        $author = (new User())
            ->setEmail('author@test.local')
            ->setPassword('hash')
            ->setRoles(['ROLE_USER']);
        $this->setId($author, 21);

        $poem = (new Poem())
            ->setAuthor($author)
            ->setTitle('Titre')
            ->setContent('Texte');
        $this->setId($poem, 8);

        $existing = (new FeatherVote())
            ->setVoter($voter)
            ->setPoem($poem)
            ->setFeatherType(FeatherType::SILVER);

        $this->userRepository->method('getById')->with(20)->willReturn($voter);
        $this->poemRepository->method('getById')->with(8)->willReturn($poem);
        $this->featherVoteRepository->method('findOneByVoterAndPoem')->willReturn($existing);
        $this->featherVoteRepository
            ->expects($this->once())
            ->method('save')
            ->with($existing);

        ## --------| Act |-------- ##
        $result = $this->service->castVote(20, 8, FeatherType::GOLD);

        ## --------| Assert |-------- ##
        $this->assertFalse($result['created']);
        $this->assertSame(FeatherType::GOLD, $existing->getFeatherType());
    }

    /**
     * 🎯 forbid voting for own poem.
     */
    public function test_cast_vote_for_own_poem_throws(): void
    {
        ## --------| Arrange |-------- ##
        $voter = (new User())
            ->setEmail('owner@test.local')
            ->setPassword('hash')
            ->setRoles(['ROLE_USER']);
        $this->setId($voter, 30);

        $poem = (new Poem())
            ->setAuthor($voter)
            ->setTitle('Titre')
            ->setContent('Texte');
        $this->setId($poem, 9);

        $this->userRepository->method('getById')->with(30)->willReturn($voter);
        $this->poemRepository->method('getById')->with(9)->willReturn($poem);

        ## --------| Act |-------- ##
        $this->expectException(CannotVoteOwnPoemException::class);
        $this->service->castVote(30, 9, FeatherType::BRONZE);
    }

    /**
     * 🎯 throw when vote not found by id.
     */
    public function test_get_vote_or_fail_throws_when_missing(): void
    {
        ## --------| Arrange |-------- ##
        $this->featherVoteRepository->method('getById')->with(404)->willReturn(null);

        ## --------| Act |-------- ##
        $this->expectException(FeatherVoteNotFoundException::class);
        $this->service->getVoteOrFail(404);
    }

    /**
     * 🎯 throw when casting vote on unknown poem.
     */
    public function test_cast_vote_throws_when_poem_missing(): void
    {
        ## --------| Arrange |-------- ##
        $voter = (new User())
            ->setEmail('voter@test.local')
            ->setPassword('hash')
            ->setRoles(['ROLE_USER']);
        $this->setId($voter, 40);

        $this->userRepository->method('getById')->with(40)->willReturn($voter);
        $this->poemRepository->method('getById')->with(999)->willReturn(null);

        ## --------| Act |-------- ##
        $this->expectException(PoemNotFoundException::class);
        $this->service->castVote(40, 999, FeatherType::GOLD);
    }

    /**
     * 🎯 throw when casting vote with unknown user.
     */
    public function test_cast_vote_throws_when_user_missing(): void
    {
        ## --------| Arrange |-------- ##
        $this->userRepository->method('getById')->with(41)->willReturn(null);

        ## --------| Act |-------- ##
        $this->expectException(UserNotFoundException::class);
        $this->service->castVote(41, 1, FeatherType::GOLD);
    }

    private function setId(object $entity, int $id): void
    {
        $ref = new \ReflectionClass($entity);
        if ($ref->hasProperty('id')) {
            $prop = $ref->getProperty('id');
            $prop->setAccessible(true);
            $prop->setValue($entity, $id);
        }
    }
}
