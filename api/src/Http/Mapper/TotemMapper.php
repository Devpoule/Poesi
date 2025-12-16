<?php

namespace App\Http\Mapper;

use App\Domain\Entity\Totem;

/**
 * Maps Totem domain entities to API-friendly arrays.
 */
final class TotemMapper
{
    /**
     * Convert a Totem entity into an array representation for the API.
     *
     * @param Totem $totem
     *
     * @return array{
     *   id: int|null,
     *   name: string,
     *   description: string|null,
     *   picture: string|null
     * }
     */
    public function toArray(Totem $totem): array
    {
        return [
            'id' => $totem->getId(),
            'name' => $totem->getName(),
            'description' => $totem->getDescription(),
            'picture' => $totem->getPicture(),
        ];
    }

    /**
     * Convert a list of Totem entities into a list of arrays.
     *
     * @param Totem[] $totems
     *
     * @return array<int, array<string, mixed>>
     */
    public function toCollection(array $totems): array
    {
        $result = [];

        foreach ($totems as $totem) {
            if (!$totem instanceof Totem) {
                continue;
            }

            $result[] = $this->toArray($totem);
        }

        return $result;
    }
}
