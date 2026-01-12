<?php

namespace App\Domain\Service;

use App\Domain\Entity\FeatherVote;
use App\Domain\Enum\FeatherType;
use App\Domain\Exception\CannotVote\CannotVoteOwnPoemException;
use App\Domain\Exception\NotFound\FeatherVoteNotFoundException;
use App\Domain\Exception\NotFound\PoemNotFoundException;
use App\Domain\Exception\NotFound\UserNotFoundException;
use App\Domain\Repository\FeatherVoteRepositoryInterface;
use App\Domain\Repository\PoemRepositoryInterface;
use App\Domain\Repository\UserRepositoryInterface;

/**
 * Domain service managing feather votes between users and poems.
 *
 * Policy:
 * - One vote per (voter, poem).
 * - If the vote already exists, its feather type is updated instead of creating a duplicate.
 */
final class FeatherVoteService
{
    public function __construct(
        private FeatherVoteRepositoryInterface $featherVoteRepository,
        private UserRepositoryInterface $userRepository,
        private PoemRepositoryInterface $poemRepository,
    ) {
    }

    /**
     * Return all votes.
     *
     * @return FeatherVote[]
     */
    public function listAll(): array
    {
        return $this->featherVoteRepository->findAll();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function listPage(int $limit, int $offset, string $sort, string $direction): array
    {
        return $this->featherVoteRepository->findListPage($limit, $offset, $sort, $direction);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function listVotesForPoemPage(int $poemId, int $limit, int $offset, string $sort, string $direction): array
    {
        $poem = $this->poemRepository->getById($poemId);

        if ($poem === null) {
            throw new PoemNotFoundException('Poem not found for id ' . $poemId . '.');
        }

        return $this->featherVoteRepository->findListByPoemPage($poem, $limit, $offset, $sort, $direction);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function listVotesByUserPage(int $userId, int $limit, int $offset, string $sort, string $direction): array
    {
        $user = $this->userRepository->getById($userId);

        if ($user === null) {
            throw new UserNotFoundException('User not found for id ' . $userId . '.');
        }

        return $this->featherVoteRepository->findListByVoterPage($user, $limit, $offset, $sort, $direction);
    }

    public function countVotes(): int
    {
        return $this->featherVoteRepository->countAll();
    }

    public function countVotesForPoem(int $poemId): int
    {
        $poem = $this->poemRepository->getById($poemId);

        if ($poem === null) {
            throw new PoemNotFoundException('Poem not found for id ' . $poemId . '.');
        }

        return $this->featherVoteRepository->countByPoem($poem);
    }

    public function countVotesByUser(int $userId): int
    {
        $user = $this->userRepository->getById($userId);

        if ($user === null) {
            throw new UserNotFoundException('User not found for id ' . $userId . '.');
        }

        return $this->featherVoteRepository->countByVoter($user);
    }

    /**
     * Retrieve a vote by id or throw.
     *
     * @param int $voteId
     *
     * @return FeatherVote
     */
    public function getVoteOrFail(int $voteId): FeatherVote
    {
        $vote = $this->featherVoteRepository->getById($voteId);

        if ($vote === null) {
            throw new FeatherVoteNotFoundException('FeatherVote not found for id ' . $voteId . '.');
        }

        return $vote;
    }

    /**
     * Cast (or update) a feather vote on a poem by a given user.
     *
     * @param int         $voterUserId
     * @param int         $poemId
     * @param FeatherType $featherType
     *
     * @return array{vote: FeatherVote, created: bool}
     */
    public function castVote(
        int $voterUserId,
        int $poemId,
        FeatherType $featherType
    ): array {
        $user = $this->userRepository->getById($voterUserId);
        if ($user === null) {
            throw new UserNotFoundException('User not found for id ' . $voterUserId . '.');
        }

        $poem = $this->poemRepository->getById($poemId);
        if ($poem === null) {
            throw new PoemNotFoundException('Poem not found for id ' . $poemId . '.');
        }

        $poemAuthor = $poem->getAuthor();
        if ($poemAuthor !== null && $poemAuthor->getId() === $user->getId()) {
            throw new CannotVoteOwnPoemException('Users cannot vote for their own poems.');
        }

        $existing = $this->featherVoteRepository->findOneByVoterAndPoem($user, $poem);

        if ($existing !== null) {
            $existing->setFeatherType($featherType);
            $this->featherVoteRepository->save($existing);

            return ['vote' => $existing, 'created' => false];
        }

        $vote = new FeatherVote();
        $vote->setVoter($user);
        $vote->setPoem($poem);
        $vote->setFeatherType($featherType);

        $this->featherVoteRepository->save($vote);

        return ['vote' => $vote, 'created' => true];
    }

    /**
     * Retrieve all votes for a given poem.
     *
     * @param int $poemId
     *
     * @return FeatherVote[]
     */
    public function listVotesForPoem(int $poemId): array
    {
        $poem = $this->poemRepository->getById($poemId);
        if ($poem === null) {
            throw new PoemNotFoundException('Poem not found for id ' . $poemId . '.');
        }

        return $this->featherVoteRepository->findByPoem($poem);
    }

    /**
     * Retrieve all votes cast by a given user.
     *
     * @param int $userId
     *
     * @return FeatherVote[]
     */
    public function listVotesByUser(int $userId): array
    {
        $user = $this->userRepository->getById($userId);
        if ($user === null) {
            throw new UserNotFoundException('User not found for id ' . $userId . '.');
        }

        return $this->featherVoteRepository->findByVoter($user);
    }

    /**
     * Delete a vote by id.
     *
     * @param int $voteId
     *
     * @return void
     */
    public function deleteVote(int $voteId): void
    {
        $vote = $this->getVoteOrFail($voteId);

        $this->featherVoteRepository->delete($vote);
    }
}
