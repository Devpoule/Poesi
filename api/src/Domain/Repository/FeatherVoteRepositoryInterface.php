<?php

namespace App\Domain\Repository;

use App\Domain\Entity\FeatherVote;
use App\Domain\Entity\Poem;
use App\Domain\Entity\User;

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
     * Returns paginated votes list (minimal fields).
     *
     * @return list<array{
     *     id:int,
     *     featherType:mixed,
     *     createdAt:\DateTimeImmutable,
     *     updatedAt:\DateTimeImmutable,
     *     voterId:int|null,
     *     voterPseudo:string|null,
     *     poemId:int|null,
     *     poemTitle:string|null
     * }>
     */
    public function findListPage(int $limit, int $offset, string $sort, string $direction): array;

    /**
     * Returns paginated votes for a given poem.
     *
     * @return list<array<string, mixed>>
     */
    public function findListByPoemPage(Poem $poem, int $limit, int $offset, string $sort, string $direction): array;

    /**
     * Returns paginated votes by a given user.
     *
     * @return list<array<string, mixed>>
     */
    public function findListByVoterPage(User $voter, int $limit, int $offset, string $sort, string $direction): array;

    public function countAll(): int;
    public function countByPoem(Poem $poem): int;
    public function countByVoter(User $voter): int;

    /**
     * Returns votes for a given poem.
     *
     * @param Poem $poem
     *
     * @return FeatherVote[]
     */
    public function findByPoem(Poem $poem): array;

    /**
     * Returns votes cast by a given user.
     *
     * @param User $voter
     *
     * @return FeatherVote[]
     */
    public function findByVoter(User $voter): array;

    /**
     * Returns the vote for (voter, poem) if it exists.
     *
     * @param User $voter
     * @param Poem   $poem
     *
     * @return FeatherVote|null
     */
    public function findOneByVoterAndPoem(User $voter, Poem $poem): ?FeatherVote;

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
