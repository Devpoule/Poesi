<?php

namespace App\Http\Request\Poem;

use App\Domain\Enum\MoodColor;
use App\Http\Exception\ValidationException;
use App\Http\Request\JsonRequestDecoder;
use App\Http\Request\RequestPayload;
use Symfony\Component\HttpFoundation\Request;

final class CreatePoemRequest
{
    private ?int $userId;
    private string $title;
    private string $content;
    private MoodColor $moodColor;

    private function __construct(?int $userId, string $title, string $content, MoodColor $moodColor)
    {
        $this->userId = $userId;
        $this->title = $title;
        $this->content = $content;
        $this->moodColor = $moodColor;
    }

    public static function fromHttpRequest(Request $request): self
    {
        $payload = JsonRequestDecoder::decodeObjectOrFail($request);
        $errors = [];

        $userId = null;
        if (\array_key_exists('userId', $payload)) {
            if ($payload['userId'] !== null) {
                $userId = RequestPayload::getPositiveInt($payload, 'userId');
                if ($userId === null) {
                    $errors['userId'][] = 'UserId must be a positive integer.';
                }
            }
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
        return new self($userId, $title, $content, $moodColor);
    }

    public function getUserId(): ?int
    {
        return $this->userId;
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
