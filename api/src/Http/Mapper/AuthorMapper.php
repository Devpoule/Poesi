<?php

namespace App\Http\Mapper;

use App\Domain\Entity\Author;

/**
 * Maps Author domain entities to array structures
 * that are safe and convenient for JSON API responses.
 */
class AuthorMapper
{
    /**
     * Transform an Author entity into a flat array representation.
     *
     * @param Author $author
     *
     * @return array<string, mixed>
     */
    public function toArray(Author $author): array
    {
        $totem = $author->getTotem();

        return [
            'id'        => $author->getId(),
            'pseudo'    => $author->getPseudo(),
            'email'     => $author->getEmail(),
            'moodColor' => $author->getMoodColor()->value,
            'totem'     => $totem !== null ? [
                'id'   => $totem->getId(),
                'name' => $totem->getName(),
            ] : null,
            'createdAt' => $author->getCreatedAt()->format(\DATE_ATOM),
        ];
    }

    /**
     * Transform a collection of Author entities into an array of arrays.
     *
     * @param iterable<Author> $authors
     *
     * @return array<int, array<string, mixed>>
     */
    public function toCollection(iterable $authors): array
    {
        $result = [];

        foreach ($authors as $author) {
            $result[] = $this->toArray($author);
        }

        return $result;
    }
}
