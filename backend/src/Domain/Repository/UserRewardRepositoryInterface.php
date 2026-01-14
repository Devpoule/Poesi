<?php

namespace App\Domain\Repository;

use App\Domain\Entity\User;
use App\Domain\Entity\UserReward;
use App\Domain\Entity\Reward;

/**
 * @extends EntityRepositoryInterface<UserReward>
 */
interface UserRewardRepositoryInterface extends EntityRepositoryInterface
{
    /**
     * Find all reward links for a given user.
     *
     * @param User $user
     *
     * @return UserReward[]
     */
    public function findByUser(User $user): array;

    /**
     * Find a specific user->reward link (to prevent duplicates).
     *
     * @param User $user
     * @param Reward $reward
     *
     * @return UserReward|null
     */
    public function findOneByUserAndReward(User $user, Reward $reward): ?UserReward;

    /**
     * Persist and flush a UserReward.
     *
     * @param object $userReward
     *
     * @return void
     */
    public function save(object $userReward): void;

    /**
     * Remove and flush a UserReward.
     *
     * @param object $userReward
     *
     * @return void
     */
    public function delete(object $userReward): void;
}
