<?php

namespace App\Http\Response;

final class UserOptionResponse
{
    /**
     * @param array{id:int,pseudo:string|null,email:string} $row
     *
     * @return array{id:int,label:string}
     */
    private function item(array $row): array
    {
        $pseudo = $row['pseudo'] ?? null;
        $email = (string) $row['email'];
        $label = $pseudo !== null && $pseudo !== '' ? $pseudo : $email;

        return [
            'id' => (int) $row['id'],
            'label' => $label,
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
