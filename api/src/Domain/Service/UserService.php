<?php

namespace App\Domain\Service;

use App\Domain\Entity\User;
use App\Domain\Exception\Conflict\EmailAlreadyUsedException;
use App\Domain\Exception\NotFound\UserNotFoundException;
use App\Domain\Repository\UserRepositoryInterface;

/**
 * Domain service responsible for basic User lifecycle.
 */
final class UserService
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
    }

    /**
     * @param string   $email
     * @param string   $hashedPassword
     * @param string[] $roles
     */
    public function createUser(string $email, string $hashedPassword, array $roles = ['ROLE_USER']): User
    {
        $existing = $this->userRepository->findOneByEmail($email);
        if ($existing !== null) {
            throw new EmailAlreadyUsedException($email);
        }

        $user = new User();
        $user->setEmail($email);
        $user->setPassword($hashedPassword);
        $user->setRoles($roles);

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
     * @param string[]|null $roles
     */
    public function updateUser(
        int $userId,
        ?string $email = null,
        ?string $hashedPassword = null,
        ?array $roles = null
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

        $this->userRepository->save($user);

        return $user;
    }

    public function deleteUser(int $userId): void
    {
        $user = $this->getUserOrFail($userId);

        $this->userRepository->delete($user);
    }
}
