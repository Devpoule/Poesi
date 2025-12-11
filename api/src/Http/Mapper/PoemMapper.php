<?php

namespace App\Http\Mapper;

use App\Domain\Entity\Poem;

/**
 * Maps Poem domain entities to array structures
 * appropriate for JSON API responses.
 */
class PoemMapper
{
    /**
     * Transform a Poem entity into a flat array representation.
     *
     * @param Poem $poem
     *
     * @return array<string, mixed>
     */
    public function toArray(Poem $poem): array
    {
        $author = $poem->getAuthor();

        return [
            'id'          => $poem->getId(),
            'title'       => $poem->getTitle(),
            'content'     => $poem->getContent(),
            'status'      => $poem->getStatus()->value,
            'moodColor'   => $poem->getMoodColor()->value,
            'author'      => $author !== null ? [
                'id'     => $author->getId(),
                'pseudo' => $author->getPseudo(),
            ] : null,
            'createdAt'   => $poem->getCreatedAt()->format(\DATE_ATOM),
            'publishedAt' => $poem->getPublishedAt()?->format(\DATE_ATOM),
            // You can easily extend this with counters, e.g. number of votes.
        ];
    }

    /**
     * Transform a collection of Poem entities into an array of arrays.
     *
     * @param iterable<Poem> $poems
     *
     * @return array<int, array<string, mixed>>
     */
    public function toCollection(iterable $poems): array
    {
        $result = [];

        foreach ($poems as $poem) {
            $result[] = $this->toArray($poem);
        }

        return $result;
    }
}
