<?php

namespace App\Http\Response;

use App\Domain\Entity\Relic;

final class RelicResponse
{
    /**
     * @return array{
     *   id:int|null,
     *   key:string,
     *   label:string,
     *   description:string|null,
     *   picture:string|null,
     *   rarity:string
     * }
     */
    public function item(Relic $relic): array
    {
        return [
            'id'          => $relic->getId(),
            'key'         => $relic->getKey(),
            'label'       => $relic->getLabel(),
            'description' => $relic->getDescription(),
            'picture'     => $relic->getPicture(),
            'rarity'      => $relic->getRarity(),
        ];
    }

    /**
     * @param iterable<Relic> $relics
     * @return array<int, array<string, mixed>>
     */
    public function collection(iterable $relics): array
    {
        $result = [];
        foreach ($relics as $relic) {
            $result[] = $this->item($relic);
        }
        return $result;
    }
}
