<?php

namespace App\Http\Response;

use App\Domain\Entity\Totem;

/**
 * Builds API-friendly payloads for Totem entities.
 */
class TotemResponseFactory
{
    /**
     * Transform a single Totem entity into an array payload.
     *
     * @return array<string,mixed>
     */
    public static function fromEntity(Totem $totem): array
    {
        return [
            'id'          => $totem->getId(),
            'name'        => $totem->getName(),
            'description' => $totem->getDescription(),
            'picture'     => $totem->getPicture(),
        ];
    }

    /**
     * Transform a list of Totem entities into an array payload.
     *
     * @param iterable<Totem> $totems
     *
     * @return array<int,array<string,mixed>>
     */
    public static function fromCollection(iterable $totems): array
    {
        $items = [];

        foreach ($totems as $totem) {
            $items[] = self::fromEntity($totem);
        }

        return $items;
    }
}
