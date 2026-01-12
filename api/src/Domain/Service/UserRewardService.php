<?php

namespace App\Domain\Service;

use App\Domain\Entity\UserReward;
use App\Domain\Exception\NotFound\RewardNotFoundException;
use App\Domain\Exception\NotFound\UserNotFoundException;
use App\Domain\Exception\NotFound\UserRewardNotFoundException;
use App\Domain\Repository\RewardRepositoryInterface;
use App\Domain\Repository\UserRepositoryInterface;
use App\Domain\Repository\UserRewardRepositoryInterface;

/**
 * Domain service responsible for managing rewards assigned to users.
 *
 * Important rule enforced here:
 * - No duplicate association between the same user and reward.
 */
final class UserRewardService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private RewardRepositoryInterface $rewardRepository,
        private UserRewardRepositoryInterface $userRewardRepository,
    ) {
    }

    /**
     * List all rewards assigned to a user.
     *
     * @param int $userId
     *
     * @return UserReward[]
     */
    public function listForUser(int $userId): array
    {
        $user = $this->userRepository->getById($userId);

        if ($user === null) {
            throw new UserNotFoundException('User not found for id ' . $userId . '.');
        }

        return $this->userRewardRepository->findByUser($user);
    }

    /**
     * Assign a reward (by code) to a user.
     * If the association already exists, it returns the existing one.
     *
     * @param int    $userId
     * @param string $rewardCode
     *
     * @return UserReward
     */
    public function assign(int $userId, string $rewardCode): UserReward
    {
        $user = $this->userRepository->getById($userId);

        if ($user === null) {
            throw new UserNotFoundException('User not found for id ' . $userId . '.');
        }

        $reward = $this->rewardRepository->findOneByCode($rewardCode);

        if ($reward === null) {
            throw new RewardNotFoundException('Reward not found for code ' . $rewardCode . '.');
        }

        $existing = $this->userRewardRepository->findOneByUserAndReward($user, $reward);
        if ($existing !== null) {
            return $existing;
        }

        $userReward = new UserReward();
        $userReward->setUser($user);
        $userReward->setReward($reward);

        $this->userRewardRepository->save($userReward);

        return $userReward;
    }

    /**
     * Retrieve a UserReward by id or throw.
     *
     * @param int $userRewardId
     *
     * @return UserReward
     */
    public function getOrFail(int $userRewardId): UserReward
    {
        $userReward = $this->userRewardRepository->getById($userRewardId);

        if ($userReward === null) {
            throw new UserRewardNotFoundException('UserReward not found for id ' . $userRewardId . '.');
        }

        return $userReward;
    }

    /**
     * Delete a UserReward link by id.
     *
     * @param int $userRewardId
     *
     * @return void
     */
    public function deleteById(int $userRewardId): void
    {
        $userReward = $this->getOrFail($userRewardId);

        $this->userRewardRepository->delete($userReward);
    }
}
