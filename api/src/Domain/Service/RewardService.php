<?php

namespace App\Domain\Service;

use App\Domain\Entity\AuthorReward;
use App\Domain\Entity\Reward;
use App\Domain\Exception\AuthorNotFoundException;
use App\Domain\Exception\AuthorRewardNotFoundException;
use App\Domain\Exception\RewardNotFoundException;
use App\Domain\Repository\AuthorRepositoryInterface;
use App\Domain\Repository\AuthorRewardRepositoryInterface;
use App\Domain\Repository\RewardRepositoryInterface;

/**
 * Domain service responsible for managing rewards and associations to authors.
 */
class RewardService
{
    public function __construct(
        private RewardRepositoryInterface $rewardRepository,
        private AuthorRepositoryInterface $authorRepository,
        private AuthorRewardRepositoryInterface $authorRewardRepository,
    ) {
    }

    /**
     * Create a new Reward with the given code and label.
     *
     * @param string $code
     * @param string $label
     *
     * @return Reward
     */
    public function createReward(string $code, string $label): Reward
    {
        $reward = new Reward();
        $reward->setCode($code);
        $reward->setLabel($label);

        $this->rewardRepository->save($reward);

        return $reward;
    }

    /**
     * Retrieve a Reward by id or throw if not found.
     *
     * @param int $rewardId
     *
     * @return Reward
     */
    public function getRewardOrFail(int $rewardId): Reward
    {
        $reward = $this->rewardRepository->getById($rewardId);

        if ($reward === null) {
            throw new RewardNotFoundException('Reward not found for id ' . $rewardId . '.');
        }

        return $reward;
    }

    /**
     * List all rewards.
     *
     * @return Reward[]
     */
    public function listAllRewards(): array
    {
        return $this->rewardRepository->findAll();
    }

    /**
     * Assign an existing reward (by code) to an author.
     *
     * @param int    $authorId
     * @param string $rewardCode
     *
     * @return AuthorReward
     */
    public function assignRewardToAuthor(int $authorId, string $rewardCode): AuthorReward
    {
        $author = $this->authorRepository->getById($authorId);

        if ($author === null) {
            throw new AuthorNotFoundException('Author not found for id ' . $authorId . '.');
        }

        $reward = $this->rewardRepository->findOneByCode($rewardCode);

        if ($reward === null) {
            throw new RewardNotFoundException('Reward not found for code ' . $rewardCode . '.');
        }

        $authorReward = new AuthorReward();
        $authorReward->setAuthor($author);
        $authorReward->setReward($reward);

        $this->authorRewardRepository->save($authorReward);

        return $authorReward;
    }

    /**
     * List all rewards assigned to a given author.
     *
     * @param int $authorId
     *
     * @return AuthorReward[]
     */
    public function listRewardsForAuthor(int $authorId): array
    {
        $author = $this->authorRepository->getById($authorId);

        if ($author === null) {
            throw new AuthorNotFoundException('Author not found for id ' . $authorId . '.');
        }

        return $this->authorRewardRepository->findByAuthor($author);
    }

    /**
     * Update a Reward properties (code / label).
     * Only non-null parameters are updated.
     *
     * @param int         $rewardId
     * @param string|null $code
     * @param string|null $label
     *
     * @return Reward
     */
    public function updateReward(
        int $rewardId,
        ?string $code = null,
        ?string $label = null
    ): Reward {
        $reward = $this->getRewardOrFail($rewardId);

        if ($code !== null) {
            $reward->setCode($code);
        }

        if ($label !== null) {
            $reward->setLabel($label);
        }

        $this->rewardRepository->save($reward);

        return $reward;
    }

    /**
     * Delete a Reward by id.
     *
     * @param int $rewardId
     *
     * @return void
     */
    public function deleteReward(int $rewardId): void
    {
        $reward = $this->getRewardOrFail($rewardId);

        $this->rewardRepository->delete($reward);
    }

    /**
     * Delete a specific AuthorReward link by id.
     *
     * @param int $authorRewardId
     *
     * @return void
     */
    public function deleteAuthorReward(int $authorRewardId): void
    {
        $authorReward = $this->authorRewardRepository->getById($authorRewardId);

        if ($authorReward === null) {
            throw new AuthorRewardNotFoundException('AuthorReward not found for id ' . $authorRewardId . '.');
        }

        $this->authorRewardRepository->delete($authorReward);
    }
}
