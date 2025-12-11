<?php

namespace App\Http\Response;

use App\Domain\Entity\Poem;

/**
 * Builds API-friendly payloads for Poem entities.
 */
class PoemResponseFactory
{
    /**
     * Transform a single Poem entity into an array payload.
     *
     * @return array<string,mixed>
     */
    public static function fromEntity(Poem $poem): array
    {
        return [
            'id'          => $poem->getId(),
            'title'       => $poem->getTitle(),
            'content'     => $poem->getContent(),
            'status'      => $poem->getStatus()->value,
            'moodColor'   => $poem->getMoodColor()->value,
            'createdAt'   => $poem->getCreatedAt()->format(DATE_ATOM),
            'publishedAt' => $poem->getPublishedAt()?->format(DATE_ATOM),
            'author'      => $poem->getAuthor() ? [
                'id'     => $poem->getAuthor()->getId(),
                'pseudo' => $poem->getAuthor()->getPseudo(),
            ] : null,
        ];
    }

    /**
     * Transform a list of Poem entities into an array payload.
     *
     * @param iterable<Poem> $poems
     *
     * @return array<int,array<string,mixed>>
     */
    public static function fromCollection(iterable $poems): array
    {
        $items = [];

        foreach ($poems as $poem) {
            $items[] = self::fromEntity($poem);
        }

        return $items;
    }
}
