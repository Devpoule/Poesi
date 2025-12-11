<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Reward;

/**
 * @extends EntityRepositoryInterface<Reward>
 */
interface RewardRepositoryInterface extends EntityRepositoryInterface
{
    /**
     * Find a reward by its technical code.
     *
     * @param string $code
     *
     * @return Reward|null
     */
    public function findOneByCode(string $code): ?Reward;

    /**
     * Find all rewards.
     *
     * @return Reward[]
     */
    public function findAll(): array;

    /**
     * Persist the given reward.
     *
     * @param Reward $reward
     *
     * @return void
     */
    public function save(object $reward): void;

    /**
     * Remove the given reward.
     *
     * @param Reward $reward
     *
     * @return void
     */
    public function delete(object $reward): void;
}
