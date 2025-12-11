<?php

namespace App\Http\Dto\Response;

use App\Domain\Entity\Poem;

/**
 * Represents the JSON response for a Poem resource.
 */
class PoemResponse
{
    public function __construct(
        public readonly int $id,
        public readonly int $authorId,
        public readonly string $authorPseudo,
        public readonly string $title,
        public readonly string $content,
        public readonly string $moodColor,
        public readonly string $status,
        public readonly string $createdAt,
        public readonly ?string $publishedAt,
    ) {
    }

    /**
     * Build a response DTO from a Poem entity.
     */
    public static function fromPoem(Poem $poem): self
    {
        return new self(
            id: $poem->getId() ?? 0,
            authorId: $poem->getAuthor()?->getId() ?? 0,
            authorPseudo: $poem->getAuthor()?->getPseudo() ?? '',
            title: $poem->getTitle(),
            content: $poem->getContent(),
            moodColor: $poem->getMoodColor()->value,
            status: $poem->getStatus()->value,
            createdAt: $poem->getCreatedAt()->format(\DateTimeInterface::ATOM),
            publishedAt: $poem->getPublishedAt()?->format(\DateTimeInterface::ATOM),
        );
    }

    /**
     * Convert the DTO to a plain array for JSON encoding.
     *
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return [
            'id'           => $this->id,
            'authorId'     => $this->authorId,
            'authorPseudo' => $this->authorPseudo,
            'title'        => $this->title,
            'content'      => $this->content,
            'moodColor'    => $this->moodColor,
            'status'       => $this->status,
            'createdAt'    => $this->createdAt,
            'publishedAt'  => $this->publishedAt,
        ];
    }
}
