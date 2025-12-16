<?php

namespace App\Http\Mapper;

use App\Domain\Entity\Author;

/**
 * Maps Author domain entities to API-friendly arrays.
 */
final class AuthorMapper
{
    /**
     * Convert an Author entity into an array representation for the API.
     *
     * @return array{
     *   id: int|null,
     *   pseudo: string,
     *   email: string,
     *   moodColor: string|null,
     *   createdAt: string,
     *   totem: array{id:int|null,name:string}|null
     * }
     */
    public function toArray(Author $author): array
    {
        $moodColor = $author->getMoodColor();

        $totem = $author->getTotem();

        return [
            'id' => $author->getId(),
            'pseudo' => $author->getPseudo(),
            'email' => $author->getEmail(),
            'moodColor' => $moodColor !== null ? $moodColor->value : null,
            'createdAt' => $author->getCreatedAt()->format(\DateTimeInterface::ATOM),
            'totem' => $totem !== null ? [
                'id' => $totem->getId(),
                'name' => $totem->getName(),
            ] : null,
        ];
    }

    /**
     * Convert a list of Author entities into a list of arrays.
     *
     * @param Author[] $authors
     *
     * @return array<int, array<string, mixed>>
     */
    public function toCollection(array $authors): array
    {
        $result = [];

        foreach ($authors as $author) {
            if (!$author instanceof Author) {
                continue;
            }

            $result[] = $this->toArray($author);
        }

        return $result;
    }
}
