<?php

namespace App\Http\Response;

final class PoemListResponse
{
    /**
     * @param array{
     *   id:int,
     *   title:string,
     *   status:mixed,
     *   moodColor:mixed,
     *   symbolType:mixed,
     *   createdAt:\DateTimeInterface,
     *   publishedAt:\DateTimeInterface|null,
     *   authorId:int|null,
     *   authorPseudo:string|null,
     *   authorTotemId:int|null
     * } $row
     *
     * @return array{
     *   id:int,
     *   title:string,
     *   status:string|null,
     *   moodColor:string|null,
     *   symbolType:string|null,
     *   createdAt:string|null,
     *   publishedAt:string|null,
     *   user:array{id:int|null,pseudo:string|null,totemId:int|null}|null
     * }
     */
    private function item(array $row): array
    {
        $status = EnumNormalizer::toString($row['status'] ?? null);
        $moodColor = EnumNormalizer::toString($row['moodColor'] ?? null);
        $symbolType = EnumNormalizer::toString($row['symbolType'] ?? null);

        $createdAt = $row['createdAt'] instanceof \DateTimeInterface
            ? $row['createdAt']->format(\DateTimeInterface::ATOM)
            : null;

        $publishedAt = $row['publishedAt'] instanceof \DateTimeInterface
            ? $row['publishedAt']->format(\DateTimeInterface::ATOM)
            : null;

        $authorId = $row['authorId'] ?? null;

        return [
            'id' => (int) $row['id'],
            'title' => (string) $row['title'],
            'status' => $status,
            'moodColor' => $moodColor,
            'symbolType' => $symbolType,
            'createdAt' => $createdAt,
            'publishedAt' => $publishedAt,
            'user' => $authorId !== null ? [
                'id' => (int) $authorId,
                'pseudo' => $row['authorPseudo'] ?? null,
                'totemId' => isset($row['authorTotemId']) ? (int) $row['authorTotemId'] : null,
            ] : null,
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
