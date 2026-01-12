<?php

namespace App\Http\Response;

use App\Domain\Entity\Mood;

final class MoodResponse
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
    public function item(Mood $mood): array
    {
        return [
            'id'          => $mood->getId(),
            'key'         => $mood->getKey(),
            'label'       => $mood->getLabel(),
            'description' => $mood->getDescription(),
            'icon'        => $mood->getIcon(),
        ];
    }

    /**
     * @param iterable<Mood> $moods
     * @return array<int, array<string, mixed>>
     */
    public function collection(iterable $moods): array
    {
        $result = [];
        foreach ($moods as $mood) {
            $result[] = $this->item($mood);
        }
        return $result;
    }
}
