<?php

namespace App\Http\Request;

use App\Domain\Enum\MoodColor;
use App\Http\Exception\ValidationException;
use Symfony\Component\HttpFoundation\Request;

/**
 * DTO-like request object responsible for:
 * - extracting payload from HTTP request
 * - validating required fields
 * - converting raw values to domain types
 *
 * This keeps controllers thin and predictable.
 */
final class CreatePoemRequest
{
    /**
     * @param int       $authorId
     * @param string    $title
     * @param string    $content
     * @param MoodColor $moodColor
     */
    private function __construct(
        private int $authorId,
        private string $title,
        private string $content,
        private MoodColor $moodColor
    ) {
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

    /**
     * Build and validate a CreatePoemRequest from an HTTP Request.
     *
     * Expected JSON payload:
     * {
     *   "authorId": 1,
     *   "title": "My poem",
     *   "content": "Text...",
     *   "moodColor": "blue"
     * }
     *
     * @param Request $request
     *
     * @return self
     *
     * @throws ValidationException When payload is invalid
     */
    public static function fromHttpRequest(Request $request): self
    {
        $payload = self::decodeJson($request);
        $errors  = [];

        $authorIdRaw = $payload['authorId'] ?? null;
        $titleRaw    = $payload['title'] ?? null;
        $contentRaw  = $payload['content'] ?? null;
        $moodRaw     = $payload['moodColor'] ?? null;

        if (!is_int($authorIdRaw)) {
            $errors['authorId'] = 'authorId must be an integer.';
        }

        if (!is_string($titleRaw) || trim($titleRaw) === '') {
            $errors['title'] = 'title is required.';
        }

        if (!is_string($contentRaw) || trim($contentRaw) === '') {
            $errors['content'] = 'content is required.';
        }

        $moodColor = null;
        if (!is_string($moodRaw) || trim($moodRaw) === '') {
            $errors['moodColor'] = 'moodColor is required.';
        } else {
            $moodColor = MoodColor::tryFrom($moodRaw);
            if ($moodColor === null) {
                $errors['moodColor'] = 'moodColor is invalid.';
            }
        }

        if ($errors !== []) {
            throw new ValidationException('Invalid payload.', $errors);
        }

        return new self(
            authorId: $authorIdRaw,
            title: trim($titleRaw),
            content: trim($contentRaw),
            moodColor: $moodColor
        );
    }

    /**
     * Decode JSON safely.
     *
     * @param Request $request
     *
     * @return array<string, mixed>
     *
     * @throws ValidationException When JSON is invalid
     */
    private static function decodeJson(Request $request): array
    {
        $content = $request->getContent();
        if (!is_string($content) || trim($content) === '') {
            throw new ValidationException('Request body must be valid JSON.', [
                'body' => 'Empty request body.'
            ]);
        }

        $decoded = json_decode($content, true);
        if (!is_array($decoded)) {
            throw new ValidationException('Request body must be valid JSON.', [
                'body' => 'Invalid JSON.'
            ]);
        }

        return $decoded;
    }
}
