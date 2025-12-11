<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Author;
use App\Domain\Entity\AuthorReward;

/**
 * @extends EntityRepositoryInterface<AuthorReward>
 */
interface AuthorRewardRepositoryInterface extends EntityRepositoryInterface
{
    /**
     * Find all reward links for a given author.
     *
     * @param Author $author
     *
     * @return AuthorReward[]
     */
    public function findByAuthor(Author $author): array;

    /**
     * Find all author rewards.
     *
     * @return AuthorReward[]
     */
    public function findAll(): array;

    /**
     * Persist the given author reward.
     *
     * @param AuthorReward $authorReward
     *
     * @return void
     */
    public function save(object $authorReward): void;

    /**
     * Remove the given author reward.
     *
     * @param AuthorReward $authorReward
     *
     * @return void
     */
    public function delete(object $authorReward): void;
}
