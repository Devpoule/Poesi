<?php

namespace App\Http\Response;

use App\Domain\Entity\Poem;

/**
 * Factory to convert Poem entities into API-safe PoemResponse DTOs.
 */
final class PoemResponseFactory
{
    /**
     * Build a response DTO for a single poem.
     *
     * @param Poem $poem
     *
     * @return PoemResponse
     */
    public static function fromEntity(Poem $poem): PoemResponse
    {
        return new PoemResponse(
            id: (int) $poem->getId(),
            authorId: (int) $poem->getAuthor()?->getId(),
            status: $poem->getStatus()->value,
            moodColor: $poem->getMoodColor()->value,
            title: $poem->getTitle(),
            content: $poem->getContent(),
            createdAt: $poem->getCreatedAt()->format(\DateTimeInterface::ATOM),
            publishedAt: $poem->getPublishedAt()?->format(\DateTimeInterface::ATOM),
            votesCount: $poem->getFeatherVotes()->count()
        );
    }

    /**
     * Build response DTOs for a list of poems.
     *
     * @param Poem[] $poems
     *
     * @return array<int, array<string, mixed>>
     */
    public static function fromEntities(array $poems): array
    {
        return array_map(
            static fn (Poem $poem) => self::fromEntity($poem)->toArray(),
            $poems
        );
    }
}
