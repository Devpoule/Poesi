<?php

declare(strict_types=1);

namespace App\Tests\Domain\Service;

use App\Domain\Entity\Poem;
use App\Domain\Entity\Totem;
use App\Domain\Entity\User;
use App\Domain\Enum\MoodColor;
use App\Domain\Exception\CannotDelete\CannotDeleteUserException;
use App\Domain\Exception\Conflict\EmailAlreadyUsedException;
use App\Domain\Exception\NotFound\TotemNotFoundException;
use App\Domain\Exception\NotFound\UserNotFoundException;
use App\Domain\Repository\TotemRepositoryInterface;
use App\Domain\Repository\UserRepositoryInterface;
use App\Domain\Service\UserService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UserServiceTest extends TestCase
{
    private UserRepositoryInterface&MockObject $userRepository;
    private TotemRepositoryInterface&MockObject $totemRepository;
    private UserService $service;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->totemRepository = $this->createMock(TotemRepositoryInterface::class);
        $this->service = new UserService($this->userRepository, $this->totemRepository);
    }

    /**
     * 🎯 create a user with pseudo, mood and totem then persist it.
     */
    public function test_create_user_persists_with_totem(): void
    {
        ## --------| Arrange |-------- ##
        $this->userRepository->method('findOneByEmail')->with('new@test.local')->willReturn(null);

        $totem = (new Totem())
            ->setKey('hawk')
            ->setName('Faucon');
        $this->setId($totem, 7);

        $this->totemRepository->method('getById')->with(7)->willReturn($totem);

        $captured = null;
        $this->userRepository
            ->expects($this->once())
            ->method('save')
            ->willReturnCallback(static function (User $user) use (&$captured): void {
                $captured = $user;
            });

        ## --------| Act |-------- ##
        $user = $this->service->createUser(
            'new@test.local',
            'hash',
            ['ROLE_USER'],
            'Nouvel Auteur',
            MoodColor::ORANGE,
            7
        );

        ## --------| Assert |-------- ##
        $this->assertSame($captured, $user);
        $this->assertSame('new@test.local', $user->getEmail());
        $this->assertSame('Nouvel Auteur', $user->getPseudo());
        $this->assertSame(MoodColor::ORANGE, $user->getMoodColor());
        $this->assertSame($totem, $user->getTotem());
    }

    /**
     * 🎯 prevent creation when email already exists.
     */
    public function test_create_user_throws_when_email_taken(): void
    {
        ## --------| Arrange |-------- ##
        $existing = (new User())
            ->setEmail('used@test.local')
            ->setPassword('hash')
            ->setRoles(['ROLE_USER']);
        $this->userRepository->method('findOneByEmail')->with('used@test.local')->willReturn($existing);

        ## --------| Act |-------- ##
        $this->expectException(EmailAlreadyUsedException::class);
        $this->service->createUser('used@test.local', 'hash');
    }

    /**
     * 🎯 prevent creation when requested totem does not exist.
     */
    public function test_create_user_throws_when_totem_missing(): void
    {
        ## --------| Arrange |-------- ##
        $this->userRepository->method('findOneByEmail')->with('totem@test.local')->willReturn(null);
        $this->totemRepository->method('getById')->with(999)->willReturn(null);

        ## --------| Act |-------- ##
        $this->expectException(TotemNotFoundException::class);
        $this->service->createUser('totem@test.local', 'hash', ['ROLE_USER'], null, null, 999);
    }

    /**
     * 🎯 throw when user id is missing.
     */
    public function test_get_user_or_fail_throws_when_missing(): void
    {
        ## --------| Arrange |-------- ##
        $this->userRepository->method('getById')->with(404)->willReturn(null);

        ## --------| Act |-------- ##
        $this->expectException(UserNotFoundException::class);
        $this->service->getUserOrFail(404);
    }

    /**
     * 🎯 prevent update when email belongs to another user.
     */
    public function test_update_user_detects_email_conflict(): void
    {
        ## --------| Arrange |-------- ##
        $target = (new User())
            ->setEmail('original@test.local')
            ->setPassword('hash')
            ->setRoles(['ROLE_USER']);
        $this->setId($target, 1);

        $other = (new User())
            ->setEmail('conflict@test.local')
            ->setPassword('hash2')
            ->setRoles(['ROLE_USER']);
        $this->setId($other, 2);

        $this->userRepository->method('getById')->with(1)->willReturn($target);
        $this->userRepository->method('findOneByEmail')->with('conflict@test.local')->willReturn($other);

        ## --------| Act |-------- ##
        $this->expectException(EmailAlreadyUsedException::class);
        $this->service->updateUser(1, 'conflict@test.local');
    }

    /**
     * 🎯 throw when updating with a missing totem.
     */
    public function test_update_user_throws_when_totem_not_found(): void
    {
        ## --------| Arrange |-------- ##
        $target = (new User())
            ->setEmail('original@test.local')
            ->setPassword('hash')
            ->setRoles(['ROLE_USER']);
        $this->setId($target, 1);

        $this->userRepository->method('getById')->with(1)->willReturn($target);
        $this->userRepository->method('findOneByEmail')->willReturn($target); // same user, allowed
        $this->totemRepository->method('getById')->with(777)->willReturn(null);

        ## --------| Act |-------- ##
        $this->expectException(TotemNotFoundException::class);
        $this->service->updateUser(1, null, null, null, null, null, 777);
    }

    /**
     * 🎯 prevent deletion when poems exist.
     */
    public function test_delete_user_with_poems_throws(): void
    {
        ## --------| Arrange |-------- ##
        $user = (new User())
            ->setEmail('poet@test.local')
            ->setPassword('hash')
            ->setRoles(['ROLE_USER']);
        $poem = (new Poem())
            ->setTitle('Titre')
            ->setContent('Texte');
        $user->addPoem($poem);

        $this->userRepository->method('getById')->with(5)->willReturn($user);
        $this->userRepository->expects($this->never())->method('delete');

        ## --------| Act |-------- ##
        $this->expectException(CannotDeleteUserException::class);
        $this->service->deleteUser(5);
    }

    /**
     * 🎯 delete user when no relations remain.
     */
    public function test_delete_user_without_relations_succeeds(): void
    {
        ## --------| Arrange |-------- ##
        $user = (new User())
            ->setEmail('simple@test.local')
            ->setPassword('hash')
            ->setRoles(['ROLE_USER']);

        $this->userRepository->method('getById')->with(6)->willReturn($user);
        $this->userRepository->expects($this->once())->method('delete')->with($user);

        ## --------| Act |-------- ##
        $this->service->deleteUser(6);

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
