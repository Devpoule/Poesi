<?php

namespace App\Http\Response;

final class RewardListResponse
{
    /**
     * @param array{id:int,code:string,label:string} $row
     *
     * @return array{id:int,code:string,label:string}
     */
    private function item(array $row): array
    {
        return [
            'id' => (int) $row['id'],
            'code' => (string) $row['code'],
            'label' => (string) $row['label'],
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
