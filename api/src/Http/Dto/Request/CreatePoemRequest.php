<?php

namespace App\Http\Dto\Request;

/**
 * Represents the incoming payload for poem creation.
 */
class CreatePoemRequest
{
    public function __construct(
        public readonly int $authorId,
        public readonly string $title,
        public readonly string $content,
        public readonly string $moodColor, // raw string, will be converted to MoodColor enum
    ) {
    }

    /**
     * Build a DTO from raw payload array.
     *
     * @param array<string,mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            authorId: (int) ($data['authorId'] ?? 0),
            title: (string) ($data['title'] ?? ''),
            content: (string) ($data['content'] ?? ''),
            moodColor: (string) ($data['moodColor'] ?? ''),
        );
    }

    /**
     * Basic payload validation.
     *
     * @return string[] List of validation error messages.
     */
    public function validate(): array
    {
        $errors = [];

        if ($this->authorId <= 0) {
            $errors[] = 'authorId must be a positive integer.';
        }

        if ($this->title === '') {
            $errors[] = 'title must not be empty.';
        }

        if ($this->content === '') {
            $errors[] = 'content must not be empty.';
        }

        if ($this->moodColor === '') {
            $errors[] = 'moodColor must not be empty.';
        }

        return $errors;
    }
}
