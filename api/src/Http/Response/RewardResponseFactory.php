<?php

namespace App\Http\Response;

use App\Domain\Entity\Reward;

/**
 * Builds API-friendly payloads for Reward entities.
 */
class RewardResponseFactory
{
    /**
     * Transform a single Reward entity into an array payload.
     *
     * @return array<string,mixed>
     */
    public static function fromEntity(Reward $reward): array
    {
        return [
            'id'    => $reward->getId(),
            'code'  => $reward->getCode(),
            'label' => $reward->getLabel(),
        ];
    }

    /**
     * Transform a list of Reward entities into an array payload.
     *
     * @param iterable<Reward> $rewards
     *
     * @return array<int,array<string,mixed>>
     */
    public static function fromCollection(iterable $rewards): array
    {
        $items = [];

        foreach ($rewards as $reward) {
            $items[] = self::fromEntity($reward);
        }

        return $items;
    }
}
