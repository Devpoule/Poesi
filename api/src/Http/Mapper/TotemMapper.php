<?php

namespace App\Http\Mapper;

use App\Domain\Entity\Totem;

/**
 * Maps Totem domain entities to array structures
 * for JSON API responses.
 */
class TotemMapper
{
    /**
     * Transform a Totem entity into a flat array representation.
     *
     * @param Totem $totem
     *
     * @return array<string, mixed>
     */
    public function toArray(Totem $totem): array
    {
        return [
            'id'          => $totem->getId(),
            'name'        => $totem->getName(),
            'description' => $totem->getDescription(),
            'picture'     => $totem->getPicture(),
        ];
    }

    /**
     * Transform a collection of Totem entities into an array of arrays.
     *
     * @param iterable<Totem> $totems
     *
     * @return array<int, array<string, mixed>>
     */
    public function toCollection(iterable $totems): array
    {
        $result = [];

        foreach ($totems as $totem) {
            $result[] = $this->toArray($totem);
        }

        return $result;
    }
}
