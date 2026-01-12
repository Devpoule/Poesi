<?php

namespace App\Domain\Repository;

use App\Domain\Entity\User;
use App\Domain\Entity\Poem;

/**
 * @extends EntityRepositoryInterface<Poem>
 */
interface PoemRepositoryInterface extends EntityRepositoryInterface
{
    /**
     * Find all poems for a given user.
     *
     * @param User $user
     *
     * @return Poem[]
     */
    public function findByUser(User $user): array;

    /**
     * Find all published poems for a given user.
     *
     * @param User $user
     *
     * @return Poem[]
     */
    public function findPublishedByUser(User $user): array;

    /**
     * Find all poems.
     *
     * @return Poem[]
     */
    public function findAll(): array;

    /**
     * Find poems list page (minimal fields).
     *
     * @return list<array{
     *     id:int,
     *     title:string,
     *     status:mixed,
     *     moodColor:mixed,
     *     symbolType:mixed,
     *     createdAt:\DateTimeImmutable,
     *     publishedAt:\DateTimeImmutable|null,
     *     authorId:int|null,
     *     authorPseudo:string|null,
     *     authorTotemId:int|null
     * }>
     */
    public function findListPage(int $limit, int $offset, string $sort, string $direction): array;

    /**
     * Find poems list page with full entities.
     *
     * @return Poem[]
     */
    public function findFullPage(int $limit, int $offset, string $sort, string $direction): array;

    /**
     * Count all poems.
     */
    public function countAll(): int;

    /**
     * Persist the given poem.
     *
     * @param Poem $poem
     *
     * @return void
     */
    public function save(object $poem): void;

    /**
     * Remove the given poem.
     *
     * @param Poem $poem
     *
     * @return void
     */
    public function delete(object $poem): void;
}
