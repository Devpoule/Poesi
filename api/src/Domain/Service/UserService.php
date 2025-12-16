<?php

namespace App\Domain\Service;

use App\Domain\Entity\User;
use App\Domain\Exception\NotFound\UserNotFoundException;
use App\Domain\Repository\UserRepositoryInterface;

/**
 * Domain service responsible for basic User lifecycle.
 */
class UserService
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
    }

    /**
     * Create a new user with the given email, hashed password and roles.
     *
     * @param string   $email
     * @param string   $hashedPassword
     * @param string[] $roles
     *
     * @return User
     */
    public function createUser(string $email, string $hashedPassword, array $roles = ['ROLE_USER']): User
    {
        $user = new User();
        $user->setEmail($email);
        $user->setPassword($hashedPassword);
        $user->setRoles($roles);

        $this->userRepository->save($user);

        return $user;
    }

    /**
     * Find a user by email.
     *
     * @param string $email
     *
     * @return User|null
     */
    public function findByEmail(string $email): ?User
    {
        return $this->userRepository->findOneByEmail($email);
    }

    /**
     * Retrieve a user by id or throw if not found.
     *
     * @param int $userId
     *
     * @return User
     */
    public function getUserOrFail(int $userId): User
    {
        $user = $this->userRepository->getById($userId);

        if ($user === null) {
            throw new UserNotFoundException('User not found for id ' . $userId . '.');
        }

        return $user;
    }

    /**
     * List all users.
     *
     * @return User[]
     */
    public function listAll(): array
    {
        return $this->userRepository->findAll();
    }

    /**
     * Update user properties.
     * Only non-null parameters are applied.
     *
     * @param int         $userId
     * @param string|null $email
     * @param string|null $hashedPassword
     * @param string[]|null $roles
     *
     * @return User
     */
    public function updateUser(
        int $userId,
        ?string $email = null,
        ?string $hashedPassword = null,
        ?array $roles = null
    ): User {
        $user = $this->getUserOrFail($userId);

        if ($email !== null) {
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

    /**
     * Delete a user by id.
     *
     * @param int $userId
     *
     * @return void
     */
    public function deleteUser(int $userId): void
    {
        $user = $this->getUserOrFail($userId);

        $this->userRepository->delete($user);
    }
}
