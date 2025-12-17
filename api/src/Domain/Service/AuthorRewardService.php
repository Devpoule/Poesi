<?php

namespace App\Domain\Service;

use App\Domain\Entity\AuthorReward;
use App\Domain\Exception\NotFound\AuthorNotFoundException;
use App\Domain\Exception\NotFound\AuthorRewardNotFoundException;
use App\Domain\Exception\NotFound\RewardNotFoundException;
use App\Domain\Repository\AuthorRepositoryInterface;
use App\Domain\Repository\AuthorRewardRepositoryInterface;
use App\Domain\Repository\RewardRepositoryInterface;

/**
 * Domain service responsible for managing rewards assigned to authors.
 *
 * Important rule enforced here:
 * - No duplicate association between the same author and reward.
 */
final class AuthorRewardService
{
    public function __construct(
        private AuthorRepositoryInterface $authorRepository,
        private RewardRepositoryInterface $rewardRepository,
        private AuthorRewardRepositoryInterface $authorRewardRepository,
    ) {
    }

    /**
     * List all rewards assigned to an author.
     *
     * @param int $authorId
     *
     * @return AuthorReward[]
     */
    public function listForAuthor(int $authorId): array
    {
        $author = $this->authorRepository->getById($authorId);

        if ($author === null) {
            throw new AuthorNotFoundException('Author not found for id ' . $authorId . '.');
        }

        return $this->authorRewardRepository->findByAuthor($author);
    }

    /**
     * Assign a reward (by code) to an author.
     * If the association already exists, it returns the existing one.
     *
     * @param int    $authorId
     * @param string $rewardCode
     *
     * @return AuthorReward
     */
    public function assign(int $authorId, string $rewardCode): AuthorReward
    {
        $author = $this->authorRepository->getById($authorId);

        if ($author === null) {
            throw new AuthorNotFoundException('Author not found for id ' . $authorId . '.');
        }

        $reward = $this->rewardRepository->findOneByCode($rewardCode);

        if ($reward === null) {
            throw new RewardNotFoundException('Reward not found for code ' . $rewardCode . '.');
        }

        $existing = $this->authorRewardRepository->findOneByAuthorAndReward($author, $reward);
        if ($existing !== null) {
            return $existing;
        }

        $authorReward = new AuthorReward();
        $authorReward->setAuthor($author);
        $authorReward->setReward($reward);

        $this->authorRewardRepository->save($authorReward);

        return $authorReward;
    }

    /**
     * Retrieve an AuthorReward by id or throw.
     *
     * @param int $authorRewardId
     *
     * @return AuthorReward
     */
    public function getOrFail(int $authorRewardId): AuthorReward
    {
        $authorReward = $this->authorRewardRepository->getById($authorRewardId);

        if ($authorReward === null) {
            throw new AuthorRewardNotFoundException('AuthorReward not found for id ' . $authorRewardId . '.');
        }

        return $authorReward;
    }

    /**
     * Delete an AuthorReward link by id.
     *
     * @param int $authorRewardId
     *
     * @return void
     */
    public function deleteById(int $authorRewardId): void
    {
        $authorReward = $this->getOrFail($authorRewardId);

        $this->authorRewardRepository->delete($authorReward);
    }
}
