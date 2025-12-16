<?php

namespace App\Http\Request\Poem;

use App\Domain\Enum\MoodColor;
use App\Http\Exception\ValidationException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Validates and normalizes the payload for updating a Poem.
 *
 * Expected JSON body (all optional, but at least one must be provided):
 * {
 *   "title": "New title",
 *   "content": "New content",
 *   "moodColor": "red"
 * }
 */
final class UpdatePoemRequest
{
    /**
     * @var string|null
     */
    private ?string $title;

    /**
     * @var string|null
     */
    private ?string $content;

    /**
     * @var MoodColor|null
     */
    private ?MoodColor $moodColor;

    /**
     * @var bool
     */
    private bool $hasAnyChange;

    /**
     * @param string|null    $title
     * @param string|null    $content
     * @param MoodColor|null $moodColor
     * @param bool           $hasAnyChange
     */
    private function __construct(
        ?string $title,
        ?string $content,
        ?MoodColor $moodColor,
        bool $hasAnyChange
    ) {
        $this->title        = $title;
        $this->content      = $content;
        $this->moodColor    = $moodColor;
        $this->hasAnyChange = $hasAnyChange;
    }

    /**
     * Builds an UpdatePoemRequest from an HTTP request.
     *
     * Rules:
     * - The body must be valid JSON.
     * - All fields are optional.
     * - If a key is present, it is validated.
     * - At least one updatable key must be present (title/content/moodColor).
     *
     * @param Request $request
     *
     * @throws ValidationException When JSON is invalid or payload is invalid.
     */
    public static function fromHttpRequest(Request $request): self
    {
        $payload = json_decode($request->getContent(), true);

        if (!is_array($payload)) {
            throw ValidationException::fromErrors(
                message: 'Invalid JSON payload.',
                errors: ['body' => ['Request body must be valid JSON.']]
            );
        }

        $errors = [];
        $hasAnyChange = false;

        $title = null;
        if (\array_key_exists('title', $payload)) {
            $hasAnyChange = true;

            $raw = $payload['title'];
            if ($raw === null || trim((string) $raw) === '') {
                $errors['title'][] = 'title cannot be empty when provided.';
            } else {
                $title = (string) $raw;
            }
        }

        $content = null;
        if (\array_key_exists('content', $payload)) {
            $hasAnyChange = true;

            $raw = $payload['content'];
            if ($raw === null || trim((string) $raw) === '') {
                $errors['content'][] = 'content cannot be empty when provided.';
            } else {
                $content = (string) $raw;
            }
        }

        $moodColor = null;
        if (\array_key_exists('moodColor', $payload)) {
            $hasAnyChange = true;

            $raw = $payload['moodColor'];
            if ($raw === null || trim((string) $raw) === '') {
                $errors['moodColor'][] = 'moodColor cannot be empty when provided.';
            } else {
                try {
                    $moodColor = MoodColor::from((string) $raw);
                } catch (\ValueError) {
                    $errors['moodColor'][] = 'moodColor is invalid.';
                }
            }
        }

        if ($hasAnyChange === false) {
            $errors['payload'][] = 'At least one of title, content or moodColor must be provided.';
        }

        if (!empty($errors)) {
            throw ValidationException::fromErrors(
                message: 'Invalid poem update payload.',
                errors: $errors
            );
        }

        return new self(
            title: $title,
            content: $content,
            moodColor: $moodColor,
            hasAnyChange: $hasAnyChange
        );
    }

    /**
     * Indicates whether the request contains at least one change key.
     *
     * @return bool
     */
    public function hasAnyChange(): bool
    {
        return $this->hasAnyChange;
    }

    /**
     * Returns the new title, or null if not provided.
     *
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Returns the new content, or null if not provided.
     *
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * Returns the new mood color, or null if not provided.
     *
     * @return MoodColor|null
     */
    public function getMoodColor(): ?MoodColor
    {
        return $this->moodColor;
    }
}
