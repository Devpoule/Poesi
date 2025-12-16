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
     * Returns all votes.
     *
     * @return FeatherVote[]
     */
    public function findAll(): array;

    /**
     * Returns votes for a given poem.
     *
     * @param Poem $poem
     *
     * @return FeatherVote[]
     */
    public function findByPoem(Poem $poem): array;

    /**
     * Returns votes cast by a given author.
     *
     * @param Author $voter
     *
     * @return FeatherVote[]
     */
    public function findByVoter(Author $voter): array;

    /**
     * Returns the vote for (voter, poem) if it exists.
     *
     * @param Author $voter
     * @param Poem   $poem
     *
     * @return FeatherVote|null
     */
    public function findOneByVoterAndPoem(Author $voter, Poem $poem): ?FeatherVote;

    /**
     * Persist and flush a FeatherVote.
     *
     * @param object $vote
     *
     * @return void
     */
    public function save(object $vote): void;

    /**
     * Remove and flush a FeatherVote.
     *
     * @param object $vote
     *
     * @return void
     */
    public function delete(object $vote): void;
}
