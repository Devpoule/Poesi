<?php

namespace App\Domain\Service;

use App\Domain\Entity\Reward;
use App\Domain\Exception\NotFound\RewardNotFoundException;
use App\Domain\Repository\RewardRepositoryInterface;

/**
 * Domain service responsible for Reward lifecycle.
 */
class RewardService
{
    public function __construct(
        private RewardRepositoryInterface $rewardRepository
    ) {
    }

    /**
     * Create a new reward.
     */
    public function create(string $code, string $label): Reward
    {
        $reward = new Reward();
        $reward->setCode($code);
        $reward->setLabel($label);

        $this->rewardRepository->save($reward);

        return $reward;
    }

    /**
     * Retrieve a reward or fail.
     */
    public function getOrFail(int $id): Reward
    {
        $reward = $this->rewardRepository->getById($id);

        if ($reward === null) {
            throw new RewardNotFoundException();
        }

        return $reward;
    }

    /**
     * List all rewards.
     *
     * @return Reward[]
     */
    public function listAll(): array
    {
        return $this->rewardRepository->findAll();
    }

    /**
     * @return list<array{id:int,code:string,label:string}>
     */
    public function listPage(int $limit, int $offset, string $sort, string $direction): array
    {
        return $this->rewardRepository->findPage($limit, $offset, $sort, $direction);
    }

    public function countRewards(): int
    {
        return $this->rewardRepository->countAll();
    }

    /**
     * Update a reward.
     */
    public function update(
        int $id,
        ?string $code = null,
        ?string $label = null
    ): Reward {
        $reward = $this->getOrFail($id);

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
     * Delete a reward.
     */
    public function delete(int $id): void
    {
        $reward = $this->getOrFail($id);

        $this->rewardRepository->delete($reward);
    }
}
