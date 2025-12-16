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
     */
    public function findOneByCode(string $code): ?Reward;

    /**
     * Retrieve all rewards.
     *
     * @return Reward[]
     */
    public function findAll(): array;

    /**
     * Persist a reward.
     */
    public function save(object $reward): void;

    /**
     * Delete a reward.
     */
    public function delete(object $reward): void;
}
