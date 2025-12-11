<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Author;
use App\Domain\Entity\FeatherVote;
use App\Domain\Entity\Poem;

/**
 * @extends EntityRepositoryInterface<FeatherVote>
 */
interface FeatherVoteRepositoryInterface extends EntityRepositoryInterface
{
    /**
     * Find all votes for a given poem.
     *
     * @param Poem $poem
     *
     * @return FeatherVote[]
     */
    public function findByPoem(Poem $poem): array;

    /**
     * Find all votes cast by a given author.
     *
     * @param Author $author
     *
     * @return FeatherVote[]
     */
    public function findByVoter(Author $author): array;

    /**
     * Find all feather votes.
     *
     * @return FeatherVote[]
     */
    public function findAll(): array;

    /**
     * Persist the given feather vote.
     *
     * @param FeatherVote $vote
     *
     * @return void
     */
    public function save(object $vote): void;

    /**
     * Remove the given feather vote.
     *
     * @param FeatherVote $vote
     *
     * @return void
     */
    public function delete(object $vote): void;
}
