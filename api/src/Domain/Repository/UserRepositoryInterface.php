<?php

namespace App\Domain\Repository;

use App\Domain\Entity\User;

/**
 * @extends EntityRepositoryInterface<User>
 */
interface UserRepositoryInterface extends EntityRepositoryInterface
{
    /**
     * Find a user by email.
     *
     * @param string $email
     *
     * @return User|null
     */
    public function findOneByEmail(string $email): ?User;

    /**
     * Find all users.
     *
     * @return User[]
     */
    public function findAll(): array;

    /**
     * Persist the given user.
     *
     * @param User $user
     *
     * @return void
     */
    public function save(object $user): void;

    /**
     * Remove the given user.
     *
     * @param User $user
     *
     * @return void
     */
    public function delete(object $user): void;
}
