<?php

namespace App\Http\Mapper;

use App\Domain\Entity\Reward;

/**
 * Maps Reward domain entities to array structures
 * for JSON API responses.
 */
class RewardMapper
{
    /**
     * Transform a Reward entity into a flat array representation.
     *
     * @param Reward $reward
     *
     * @return array<string, mixed>
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
     * Transform a collection of Reward entities into an array of arrays.
     *
     * @param iterable<Reward> $rewards
     *
     * @return array<int, array<string, mixed>>
     */
    public function toCollection(iterable $rewards): array
    {
        $result = [];

        foreach ($rewards as $reward) {
            $result[] = $this->toArray($reward);
        }

        return $result;
    }
}
