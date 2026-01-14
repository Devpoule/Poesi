<?php

namespace App\Domain\Service;

use App\Domain\Entity\User;
use App\Domain\Enum\MoodColor;
use App\Domain\Exception\CannotDelete\CannotDeleteUserException;
use App\Domain\Exception\Conflict\EmailAlreadyUsedException;
use App\Domain\Exception\NotFound\UserNotFoundException;
use App\Domain\Exception\NotFound\TotemNotFoundException;
use App\Domain\Repository\TotemRepositoryInterface;
use App\Domain\Repository\UserRepositoryInterface;

/**
 * Domain service responsible for basic User lifecycle.
 */
final class UserService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private TotemRepositoryInterface $totemRepository,
    ) {
    }

    /**
     * @param string   $email
     * @param string   $hashedPassword
     * @param string[] $roles
     */
    public function createUser(
        string $email,
        string $hashedPassword,
        array $roles = ['ROLE_USER'],
        ?string $pseudo = null,
        ?MoodColor $moodColor = null,
        ?int $totemId = null,
        ?string $totemKey = null
    ): User {
        $existing = $this->userRepository->findOneByEmail($email);
        if ($existing !== null) {
            throw new EmailAlreadyUsedException($email);
        }

        $user = new User();
        $user->setEmail($email);
        $user->setPassword($hashedPassword);
        $user->setRoles($roles);
        $user->setPseudo($pseudo);

        if ($moodColor !== null) {
            $user->setMoodColor($moodColor);
        }

        if ($totemId !== null) {
            $totem = $this->totemRepository->getById($totemId);
            if ($totem === null) {
                throw new TotemNotFoundException('Totem not found for id ' . $totemId . '.');
            }
            $user->setTotem($totem);
        } elseif ($totemKey !== null) {
            $totem = $this->totemRepository->getByKey($totemKey);
            if ($totem === null) {
                throw new TotemNotFoundException('Totem not found for key ' . $totemKey . '.');
            }
            $user->setTotem($totem);
        }

        $this->userRepository->save($user);

        return $user;
    }

    public function findByEmail(string $email): ?User
    {
        return $this->userRepository->findOneByEmail($email);
    }

    public function getUserOrFail(int $userId): User
    {
        $user = $this->userRepository->getById($userId);

        if ($user === null) {
            throw new UserNotFoundException('User not found for id ' . $userId . '.');
        }

        return $user;
    }

    /**
     * @return User[]
     */
    public function listAll(): array
    {
        return $this->userRepository->findAll();
    }

    /**
     * @return list<array{
     *     id:int,
     *     email:string,
     *     pseudo:string|null,
     *     roles:string[],
     *     createdAt:\DateTimeImmutable,
     *     lockedAt:\DateTimeImmutable|null,
     *     failedLoginAttempts:int
     * }>
     */
    public function listAdminSummary(): array
    {
        return $this->userRepository->findAdminList();
    }

    /**
     * @return list<array{id:int,pseudo:string|null,email:string}>
     */
    public function listOptions(): array
    {
        return $this->userRepository->findOptions();
    }

    /**
     * @return list<array{id:int,pseudo:string|null,moodColor:string,totemId:int|null}>
     */
    public function listPublicProfiles(): array
    {
        return $this->userRepository->findPublicList();
    }

    /**
     * @return list<array{
     *     id:int,
     *     email:string,
     *     pseudo:string|null,
     *     roles:string[],
     *     createdAt:\DateTimeImmutable,
     *     lockedAt:\DateTimeImmutable|null,
     *     failedLoginAttempts:int
     * }>
     */
    public function listAdminSummaryPage(int $limit, int $offset, string $sort, string $direction): array
    {
        return $this->userRepository->findAdminListPage($limit, $offset, $sort, $direction);
    }

    /**
     * @return list<array{id:int,pseudo:string|null,email:string}>
     */
    public function listOptionsPage(int $limit, int $offset, string $sort, string $direction): array
    {
        return $this->userRepository->findOptionsPage($limit, $offset, $sort, $direction);
    }

    /**
     * @return list<array{id:int,pseudo:string|null,moodColor:string,totemId:int|null}>
     */
    public function listPublicProfilesPage(int $limit, int $offset, string $sort, string $direction): array
    {
        return $this->userRepository->findPublicListPage($limit, $offset, $sort, $direction);
    }

    public function countUsers(): int
    {
        return $this->userRepository->countAll();
    }

    /**
     * @param string[]|null $roles
     */
    public function updateUser(
        int $userId,
        ?string $email = null,
        ?string $hashedPassword = null,
        ?array $roles = null,
        ?string $pseudo = null,
        ?MoodColor $moodColor = null,
        ?int $totemId = null,
        ?string $totemKey = null
    ): User {
        $user = $this->getUserOrFail($userId);

        if ($email !== null) {
            $existing = $this->userRepository->findOneByEmail($email);
            if ($existing !== null && $existing->getId() !== $user->getId()) {
                throw new EmailAlreadyUsedException($email);
            }

            $user->setEmail($email);
        }

        if ($hashedPassword !== null) {
            $user->setPassword($hashedPassword);
        }

        if ($roles !== null) {
            $user->setRoles($roles);
        }

        if ($pseudo !== null) {
            $user->setPseudo($pseudo);
        }

        if ($moodColor !== null) {
            $user->setMoodColor($moodColor);
        }

        if ($totemId !== null) {
            $totem = $this->totemRepository->getById($totemId);
            if ($totem === null) {
                throw new TotemNotFoundException('Totem not found for id ' . $totemId . '.');
            }
            $user->setTotem($totem);
        } elseif ($totemKey !== null) {
            $totem = $this->totemRepository->getByKey($totemKey);
            if ($totem === null) {
                throw new TotemNotFoundException('Totem not found for key ' . $totemKey . '.');
            }
            $user->setTotem($totem);
        }

        $this->userRepository->save($user);

        return $user;
    }

    public function deleteUser(int $userId): void
    {
        $user = $this->getUserOrFail($userId);

        if ($user->getPoems()->count() > 0) {
            throw new CannotDeleteUserException('Cannot delete user: poems exist.');
        }

        if ($user->getFeatherVotes()->count() > 0) {
            throw new CannotDeleteUserException('Cannot delete user: votes exist.');
        }

        if ($user->getUserRewards()->count() > 0) {
            throw new CannotDeleteUserException('Cannot delete user: rewards exist.');
        }

        $this->userRepository->delete($user);
    }
}
