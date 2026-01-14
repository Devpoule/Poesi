<?php

namespace App\Http\Response;

final class UserPublicResponse
{
    /**
     * @param array{id:int,pseudo:string|null,moodColor:string,totemId:int|null} $row
     *
     * @return array{id:int,pseudo:string|null,moodColor:string,totemId:int|null}
     */
    private function item(array $row): array
    {
        $moodColor = EnumNormalizer::toString($row['moodColor'] ?? null);

        return [
            'id' => (int) $row['id'],
            'pseudo' => $row['pseudo'] ?? null,
            'moodColor' => (string) $moodColor,
            'totemId' => isset($row['totemId']) ? (int) $row['totemId'] : null,
        ];
    }

    /**
     * @param list<array<string, mixed>> $rows
     *
     * @return array<int, array<string, mixed>>
     */
    public function collection(array $rows): array
    {
        $result = [];
        foreach ($rows as $row) {
            $result[] = $this->item($row);
        }

        return $result;
    }
}
