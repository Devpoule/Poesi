<?php

namespace App\Http\Mapper;

use App\Domain\Entity\Reward;

/**
 * Maps Reward entities to API arrays.
 */
final class RewardMapper
{
    /**
     * Convert a Reward into array.
     */
    public function toArray(Reward $reward): array
    {
        return [
            'id'    => $reward->getId(),
            'code'  => $reward->getCode(),
            'label' => $reward->getLabel(),
        ];
    }

    /**
     * Convert a list of rewards.
     *
     * @param Reward[] $rewards
     */
    public function toCollection(array $rewards): array
    {
        $result = [];

        foreach ($rewards as $reward) {
            if ($reward instanceof Reward) {
                $result[] = $this->toArray($reward);
            }
        }

        return $result;
    }
}
