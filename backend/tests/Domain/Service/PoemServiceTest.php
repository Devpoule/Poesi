<?php

declare(strict_types=1);

namespace App\Tests\Domain\Service;

use App\Domain\Entity\FeatherVote;
use App\Domain\Entity\Poem;
use App\Domain\Entity\Totem;
use App\Domain\Entity\User;
use App\Domain\Enum\MoodColor;
use App\Domain\Enum\PoemStatus;
use App\Domain\Exception\CannotDelete\CannotDeletePoemWithVotesException;
use App\Domain\Exception\CannotPublish\CannotPublishPoemException;
use App\Domain\Exception\CannotUpdate\CannotUpdatePoemException;
use App\Domain\Exception\NotFound\PoemNotFoundException;
use App\Domain\Exception\NotFound\UserNotFoundException;
use App\Domain\Repository\PoemRepositoryInterface;
use App\Domain\Repository\UserRepositoryInterface;
use App\Domain\Service\PoemService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PoemServiceTest extends TestCase
{
    private PoemRepositoryInterface&MockObject $poemRepository;
    private UserRepositoryInterface&MockObject $userRepository;
    private PoemService $service;

    protected function setUp(): void
    {
        $this->poemRepository = $this->createMock(PoemRepositoryInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->service = new PoemService($this->poemRepository, $this->userRepository);
    }

    /**
     * 🎯 ensure a draft is created with author, mood and status set then persisted.
     */
    public function test_create_draft_persists_poem_and_sets_defaults(): void
    {
        ## --------| Arrange |-------- ##
        $user = (new User())
            ->setEmail('author@test.local')
            ->setPassword('hash')
            ->setRoles(['ROLE_USER']);
        $this->setId($user, 10);

        $captured = null;
        $this->userRepository
            ->method('getById')
            ->with(10)
            ->willReturn($user);
        $this->poemRepository
            ->expects($this->once())
            ->method('save')
            ->willReturnCallback(static function (Poem $poem) use (&$captured): void {
                $captured = $poem;
            });

        ## --------| Act |-------- ##
        $poem = $this->service->createDraft(10, 'Titre', 'Corps', MoodColor::VIOLET);

        ## --------| Assert |-------- ##
        $this->assertSame($captured, $poem);
        $this->assertSame($user, $poem->getAuthor());
        $this->assertSame('Titre', $poem->getTitle());
        $this->assertSame('Corps', $poem->getContent());
        $this->assertSame(MoodColor::VIOLET, $poem->getMoodColor());
        $this->assertSame(PoemStatus::DRAFT, $poem->getStatus());
    }

    /**
     * 🎯 prevent draft creation when author does not exist.
     */
    public function test_create_draft_throws_when_user_missing(): void
    {
        ## --------| Arrange |-------- ##
        $this->userRepository
            ->method('getById')
            ->with(404)
            ->willReturn(null);

        ## --------| Act |-------- ##
        $this->expectException(UserNotFoundException::class);
        $this->service->createDraft(404, 'Titre', 'Corps', MoodColor::GREEN);
    }

    /**
     * 🎯 prevent publication when author totem is missing or default.
     */
    public function test_publish_requires_totem(): void
    {
        ## --------| Arrange |-------- ##
        $poem = (new Poem())
            ->setTitle('Titre')
            ->setContent('Texte');
        $user = (new User())
            ->setEmail('author@test.local')
            ->setPassword('hash')
            ->setRoles(['ROLE_USER']);
        $poem->setAuthor($user);

        $this->poemRepository
            ->method('getById')
            ->with(7)
            ->willReturn($poem);

        ## --------| Act |-------- ##
        $this->expectException(CannotPublishPoemException::class);
        $this->service->publish(7);
    }

    /**
     * 🎯 publish when totem is properly chosen, setting status and timestamp.
     */
    public function test_publish_sets_status_and_published_at(): void
    {
        ## --------| Arrange |-------- ##
        $totem = (new Totem())
            ->setKey('swan')
            ->setName('Cygne');
        $this->setId($totem, 2);

        $user = (new User())
            ->setEmail('author@test.local')
            ->setPassword('hash')
            ->setRoles(['ROLE_USER'])
            ->setTotem($totem);
        $this->setId($user, 33);

        $poem = (new Poem())
            ->setAuthor($user)
            ->setTitle('Titre')
            ->setContent('Texte');

        $this->poemRepository
            ->method('getById')
            ->with(5)
            ->willReturn($poem);
        $this->poemRepository
            ->expects($this->once())
            ->method('save')
            ->with($poem);

        ## --------| Act |-------- ##
        $published = $this->service->publish(5);

        ## --------| Assert |-------- ##
        $this->assertSame(PoemStatus::PUBLISHED, $published->getStatus());
        $this->assertInstanceOf(\DateTimeImmutable::class, $published->getPublishedAt());
    }

    /**
     * 🎯 forbid updates on already published poems.
     */
    public function test_update_poem_refuses_published_poem(): void
    {
        ## --------| Arrange |-------- ##
        $poem = (new Poem())
            ->setTitle('Titre')
            ->setContent('Texte')
            ->setStatus(PoemStatus::PUBLISHED);

        $this->poemRepository
            ->method('getById')
            ->with(9)
            ->willReturn($poem);
        $this->poemRepository
            ->expects($this->never())
            ->method('save');

        ## --------| Act |-------- ##
        $this->expectException(CannotUpdatePoemException::class);
        $this->service->updatePoem(9, 'Nouveau', null, MoodColor::BLUE);
    }

    /**
     * 🎯 forbid deletion when votes exist.
     */
    public function test_delete_poem_with_votes_throws(): void
    {
        ## --------| Arrange |-------- ##
        $poem = (new Poem())
            ->setTitle('Titre')
            ->setContent('Texte');
        $poem->addFeatherVote(new FeatherVote());

        $this->poemRepository
            ->method('getById')
            ->with(3)
            ->willReturn($poem);
        $this->poemRepository
            ->expects($this->never())
            ->method('delete');

        ## --------| Act |-------- ##
        $this->expectException(CannotDeletePoemWithVotesException::class);
        $this->service->deletePoem(3);
    }

    /**
     * 🎯 delete poem when no vote remains.
     */
    public function test_delete_poem_succeeds_without_votes(): void
    {
        ## --------| Arrange |-------- ##
        $poem = (new Poem())
            ->setTitle('Titre')
            ->setContent('Texte');

        $this->poemRepository
            ->method('getById')
            ->with(4)
            ->willReturn($poem);
        $this->poemRepository
            ->expects($this->once())
            ->method('delete')
            ->with($poem);

        ## --------| Act |-------- ##
        $this->service->deletePoem(4);

        ## --------| Assert |-------- ##
        $this->addToAssertionCount(1);
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
