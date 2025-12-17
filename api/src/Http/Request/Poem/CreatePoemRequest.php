<?php

namespace App\Http\Request\Poem;

use App\Domain\Enum\MoodColor;
use App\Http\Exception\ValidationException;
use App\Http\Request\JsonRequestDecoder;
use App\Http\Request\RequestPayload;
use Symfony\Component\HttpFoundation\Request;

final class CreatePoemRequest
{
    private int $authorId;
    private string $title;
    private string $content;
    private MoodColor $moodColor;

    private function __construct(int $authorId, string $title, string $content, MoodColor $moodColor)
    {
        $this->authorId = $authorId;
        $this->title = $title;
        $this->content = $content;
        $this->moodColor = $moodColor;
    }

    public static function fromHttpRequest(Request $request): self
    {
        $payload = JsonRequestDecoder::decodeObjectOrFail($request);
        $errors = [];

        $authorId = RequestPayload::getPositiveInt($payload, 'authorId');
        if ($authorId === null) {
            $errors['authorId'][] = 'AuthorId must be a positive integer.';
        }

        $title = RequestPayload::getTrimmedString($payload, 'title');
        if ($title === null) {
            $errors['title'][] = 'Title is required.';
        }

        $content = RequestPayload::getTrimmedString($payload, 'content');
        if ($content === null) {
            $errors['content'][] = 'Content is required.';
        }

        $moodRaw = RequestPayload::getTrimmedString($payload, 'moodColor');
        $moodColor = null;

        if ($moodRaw === null) {
            $errors['moodColor'][] = 'MoodColor is required.';
        } else {
            try {
                $moodColor = MoodColor::from($moodRaw);
            } catch (\ValueError) {
                $errors['moodColor'][] = 'MoodColor is invalid.';
            }
        }

        if ($errors !== []) {
            throw ValidationException::fromErrors(
                message: 'Invalid poem payload.',
                errors: $errors
            );
        }

        /** @var MoodColor $moodColor */
        return new self($authorId, $title, $content, $moodColor);
    }

    public function getAuthorId(): int
    {
        return $this->authorId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getMoodColor(): MoodColor
    {
        return $this->moodColor;
    }
}
