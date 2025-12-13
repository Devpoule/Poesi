<?php

namespace App\Http\Response;

use App\Domain\Entity\Poem;

/**
 * Read-only API DTO representing a Poem for output.
 *
 * This isolates your API from internal entity structure changes.
 */
final class PoemResponse
{
    /**
     * @param int         $id
     * @param int         $authorId
     * @param string      $status
     * @param string      $moodColor
     * @param string      $title
     * @param string      $content
     * @param string      $createdAt
     * @param string|null $publishedAt
     * @param int         $votesCount
     */
    public function __construct(
        public int $id,
        public int $authorId,
        public string $status,
        public string $moodColor,
        public string $title,
        public string $content,
        public string $createdAt,
        public ?string $publishedAt,
        public int $votesCount,
    ) {
    }

    /**
     * Convert to array for JsonResponse.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id'          => $this->id,
            'authorId'    => $this->authorId,
            'status'      => $this->status,
            'moodColor'   => $this->moodColor,
            'title'       => $this->title,
            'content'     => $this->content,
            'createdAt'   => $this->createdAt,
            'publishedAt' => $this->publishedAt,
            'votesCount'  => $this->votesCount,
        ];
    }
}
