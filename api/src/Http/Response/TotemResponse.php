<?php

namespace App\Http\Response;

use App\Domain\Entity\Totem;

final class TotemResponse
{
    /**
     * @return array{
     *   id:int|null,
     *   name:string,
     *   description:string|null,
     *   picture:string|null
     * }
     */
    public function item(Totem $totem): array
    {
        return [
            'id'          => $totem->getId(),
            'key'         => $totem->getKey(),
            'name'        => $totem->getName(),
            'description' => $totem->getDescription(),
            'picture'     => $totem->getPicture(),
        ];
    }

    /**
     * @param iterable<Totem> $totems
     *
     * @return array<int, array<string, mixed>>
     */
    public function collection(iterable $totems): array
    {
        $result = [];
        foreach ($totems as $totem) {
            $result[] = $this->item($totem);
        }
        return $result;
    }
}
