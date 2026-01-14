<?php

namespace App\Http\Response;

use App\Domain\Entity\Feather;

final class FeatherResponse
{
    /**
     * @return array{
     *   id:int|null,
     *   key:string,
     *   label:string,
     *   description:string|null,
     *   icon:string|null
     * }
     */
    public function item(Feather $feather): array
    {
        return [
            'id'          => $feather->getId(),
            'key'         => $feather->getKey(),
            'label'       => $feather->getLabel(),
            'description' => $feather->getDescription(),
            'icon'        => $feather->getIcon(),
        ];
    }

    /**
     * @param iterable<Feather> $feathers
     * @return array<int, array<string, mixed>>
     */
    public function collection(iterable $feathers): array
    {
        $result = [];
        foreach ($feathers as $feather) {
            $result[] = $this->item($feather);
        }
        return $result;
    }
}
