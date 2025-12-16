<?php

namespace App\Http\Request\Poem;

use App\Domain\Enum\MoodColor;
use App\Http\Exception\ValidationException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Validates and normalizes the payload for creating a Poem.
 *
 * Expected JSON body:
 * {
 *   "authorId": 1,
 *   "title": "My title",
 *   "content": "My content",
 *   "moodColor": "blue"
 * }
 */
final class CreatePoemRequest
{
    /**
     * @var int
     */
    private int $authorId;

    /**
     * @var string
     */
    private string $title;

    /**
     * @var string
     */
    private string $content;

    /**
     * @var MoodColor
     */
    private MoodColor $moodColor;

    /**
     * @param int       $authorId
     * @param string    $title
     * @param string    $content
     * @param MoodColor $moodColor
     */
    private function __construct(int $authorId, string $title, string $content, MoodColor $moodColor)
    {
        $this->authorId  = $authorId;
        $this->title     = $title;
        $this->content   = $content;
        $this->moodColor = $moodColor;
    }

    /**
     * Builds a CreatePoemRequest from an HTTP request.
     *
     * @param Request $request
     *
     * @throws ValidationException When JSON is invalid or required fields are missing/invalid.
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

        $authorId = $payload['authorId'] ?? null;
        $title    = $payload['title'] ?? null;
        $content  = $payload['content'] ?? null;
        $moodRaw  = $payload['moodColor'] ?? null;

        if ($authorId === null || !is_numeric($authorId) || (int) $authorId <= 0) {
            $errors['authorId'][] = 'authorId must be a positive integer.';
        }

        if ($title === null || trim((string) $title) === '') {
            $errors['title'][] = 'title is required.';
        }

        if ($content === null || trim((string) $content) === '') {
            $errors['content'][] = 'content is required.';
        }

        $moodColor = null;
        if ($moodRaw === null || trim((string) $moodRaw) === '') {
            $errors['moodColor'][] = 'moodColor is required.';
        } else {
            try {
                $moodColor = MoodColor::from((string) $moodRaw);
            } catch (\ValueError) {
                $errors['moodColor'][] = 'moodColor is invalid.';
            }
        }

        if (!empty($errors)) {
            throw ValidationException::fromErrors(
                message: 'Invalid poem payload.',
                errors: $errors
            );
        }

        return new self(
            authorId: (int) $authorId,
            title: (string) $title,
            content: (string) $content,
            moodColor: $moodColor
        );
    }

    /**
     * @return int
     */
    public function getAuthorId(): int
    {
        return $this->authorId;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return MoodColor
     */
    public function getMoodColor(): MoodColor
    {
        return $this->moodColor;
    }
}
