<?php

namespace App\Http\Response;

final class FeatherVoteListResponse
{
    /**
     * @param array{
     *   id:int,
     *   featherType:mixed,
     *   createdAt:\DateTimeInterface,
     *   updatedAt:\DateTimeInterface,
     *   voterId:int|null,
     *   voterPseudo:string|null,
     *   poemId:int|null,
     *   poemTitle:string|null
     * } $row
     *
     * @return array{
     *   id:int,
     *   featherType:string|null,
     *   createdAt:string,
     *   updatedAt:string,
     *   voter:array{id:int|null,pseudo:string|null,state:string},
     *   poem:array{id:int|null,title:string|null,state:string}
     * }
     */
    private function item(array $row): array
    {
        $featherType = EnumNormalizer::toString($row['featherType'] ?? null);
        $voterId = $row['voterId'] ?? null;
        $poemId = $row['poemId'] ?? null;

        return [
            'id' => (int) $row['id'],
            'featherType' => $featherType,
            'createdAt' => $row['createdAt']->format(\DateTimeInterface::ATOM),
            'updatedAt' => $row['updatedAt']->format(\DateTimeInterface::ATOM),
            'voter' => $voterId !== null
                ? ['id' => (int) $voterId, 'pseudo' => $row['voterPseudo'] ?? null, 'state' => 'known']
                : ['id' => null, 'pseudo' => null, 'state' => 'missing'],
            'poem' => $poemId !== null
                ? ['id' => (int) $poemId, 'title' => $row['poemTitle'] ?? null, 'state' => 'known']
                : ['id' => null, 'title' => null, 'state' => 'missing'],
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
