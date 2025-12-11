<?php

namespace App\Http\Response;

use App\Domain\Entity\Author;

/**
 * Builds API-friendly payloads for Author entities.
 */
class AuthorResponseFactory
{
    /**
     * Transform a single Author entity into an array payload.
     *
     * @return array<string,mixed>
     */
    public static function fromEntity(Author $author): array
    {
        return [
            'id'         => $author->getId(),
            'pseudo'     => $author->getPseudo(),
            'email'      => $author->getEmail(),
            'moodColor'  => $author->getMoodColor()->value,
            'createdAt'  => $author->getCreatedAt()->format(DATE_ATOM),
            'totem'      => $author->getTotem() !== null ? [
                'id'   => $author->getTotem()->getId(),
                'name' => $author->getTotem()->getName(),
            ] : null,
        ];
    }

    /**
     * Transform a list of Author entities into an array payload.
     *
     * @param iterable<Author> $authors
     *
     * @return array<int,array<string,mixed>>
     */
    public static function fromCollection(iterable $authors): array
    {
        $results = [];

        foreach ($authors as $author) {
            $results[] = self::fromEntity($author);
        }

        return $results;
    }
}
