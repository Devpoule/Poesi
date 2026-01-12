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
     * Return admin list rows with minimal data.
     *
     * @param int $limit
     * @param int $offset
     * @param string $sort
     * @param string $direction
     *
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
    public function findAdminListPage(int $limit, int $offset, string $sort, string $direction): array;

    /**
     * Return lightweight user options for selectors.
     *
     * @param int $limit
     * @param int $offset
     * @param string $sort
     * @param string $direction
     *
     * @return list<array{id:int,pseudo:string|null,email:string}>
     */
    public function findOptionsPage(int $limit, int $offset, string $sort, string $direction): array;

    /**
     * Return public profile rows.
     *
     * @param int $limit
     * @param int $offset
     * @param string $sort
     * @param string $direction
     *
     * @return list<array{id:int,pseudo:string|null,moodColor:string,totemId:int|null}>
     */
    public function findPublicListPage(int $limit, int $offset, string $sort, string $direction): array;

    /**
     * Count all users.
     */
    public function countAll(): int;

    /**
     * Return admin list rows with minimal data.
     *
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
    public function findAdminList(): array;

    /**
     * Return lightweight user options for selectors.
     *
     * @return list<array{id:int,pseudo:string|null,email:string}>
     */
    public function findOptions(): array;

    /**
     * Return public profile rows.
     *
     * @return list<array{id:int,pseudo:string|null,moodColor:string,totemId:int|null}>
     */
    public function findPublicList(): array;

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
