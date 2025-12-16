<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Author;
use App\Domain\Entity\AuthorReward;
use App\Domain\Entity\Reward;

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
     * Find a specific author->reward link (to prevent duplicates).
     *
     * @param Author $author
     * @param Reward $reward
     *
     * @return AuthorReward|null
     */
    public function findOneByAuthorAndReward(Author $author, Reward $reward): ?AuthorReward;

    /**
     * Persist and flush an AuthorReward.
     *
     * @param object $authorReward
     *
     * @return void
     */
    public function save(object $authorReward): void;

    /**
     * Remove and flush an AuthorReward.
     *
     * @param object $authorReward
     *
     * @return void
     */
    public function delete(object $authorReward): void;
}
