<?php

namespace App\Domain\Service;

use App\Domain\Entity\FeatherVote;
use App\Domain\Enum\FeatherType;
use App\Domain\Exception\NotFound\AuthorNotFoundException;
use App\Domain\Exception\NotFound\PoemNotFoundException;
use App\Domain\Repository\AuthorRepositoryInterface;
use App\Domain\Repository\FeatherVoteRepositoryInterface;
use App\Domain\Repository\PoemRepositoryInterface;

/**
 * Domain service managing feather votes between authors and poems.
 *
 * Policy:
 * - One vote per (voter, poem).
 * - If the vote already exists, its feather type is updated instead of creating a duplicate.
 */
final class FeatherVoteService
{
    public function __construct(
        private FeatherVoteRepositoryInterface $featherVoteRepository,
        private AuthorRepositoryInterface $authorRepository,
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
            throw new \RuntimeException('FeatherVote not found for id ' . $voteId . '.');
        }

        return $vote;
    }

    /**
     * Cast (or update) a feather vote on a poem by a given author.
     *
     * @param int         $voterAuthorId
     * @param int         $poemId
     * @param FeatherType $featherType
     *
     * @return array{vote: FeatherVote, created: bool}
     */
    public function castVote(
        int $voterAuthorId,
        int $poemId,
        FeatherType $featherType
    ): array {
        $author = $this->authorRepository->getById($voterAuthorId);
        if ($author === null) {
            throw new AuthorNotFoundException('Author not found for id ' . $voterAuthorId . '.');
        }

        $poem = $this->poemRepository->getById($poemId);
        if ($poem === null) {
            throw new PoemNotFoundException('Poem not found for id ' . $poemId . '.');
        }

        $existing = $this->featherVoteRepository->findOneByVoterAndPoem($author, $poem);

        if ($existing !== null) {
            $existing->setFeatherType($featherType);
            $this->featherVoteRepository->save($existing);

            return ['vote' => $existing, 'created' => false];
        }

        $vote = new FeatherVote();
        $vote->setVoter($author);
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
     * Retrieve all votes cast by a given author.
     *
     * @param int $authorId
     *
     * @return FeatherVote[]
     */
    public function listVotesByAuthor(int $authorId): array
    {
        $author = $this->authorRepository->getById($authorId);
        if ($author === null) {
            throw new AuthorNotFoundException('Author not found for id ' . $authorId . '.');
        }

        return $this->featherVoteRepository->findByVoter($author);
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
