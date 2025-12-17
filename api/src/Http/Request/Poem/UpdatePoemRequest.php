<?php

namespace App\Http\Request\Poem;

use App\Domain\Enum\MoodColor;
use App\Http\Exception\ValidationException;
use App\Http\Request\JsonRequestDecoder;
use App\Http\Request\RequestPayload;
use Symfony\Component\HttpFoundation\Request;

final class UpdatePoemRequest
{
    private ?string $title;
    private ?string $content;
    private ?MoodColor $moodColor;
    private bool $hasAnyChange;

    private function __construct(?string $title, ?string $content, ?MoodColor $moodColor, bool $hasAnyChange)
    {
        $this->title = $title;
        $this->content = $content;
        $this->moodColor = $moodColor;
        $this->hasAnyChange = $hasAnyChange;
    }

    public static function fromHttpRequest(Request $request): self
    {
        $payload = JsonRequestDecoder::decodeObjectOrFail($request);
        $errors = [];
        $hasAnyChange = false;

        $title = null;
        if (\array_key_exists('title', $payload)) {
            $hasAnyChange = true;
            $title = RequestPayload::getTrimmedString($payload, 'title');

            if ($title === null) {
                $errors['title'][] = 'title cannot be empty when provided.';
            }
        }

        $content = null;
        if (\array_key_exists('content', $payload)) {
            $hasAnyChange = true;
            $content = RequestPayload::getTrimmedString($payload, 'content');

            if ($content === null) {
                $errors['content'][] = 'content cannot be empty when provided.';
            }
        }

        $moodColor = null;
        if (\array_key_exists('moodColor', $payload)) {
            $hasAnyChange = true;

            $raw = RequestPayload::getTrimmedString($payload, 'moodColor');
            if ($raw === null) {
                $errors['moodColor'][] = 'moodColor cannot be empty when provided.';
            } else {
                try {
                    $moodColor = MoodColor::from($raw);
                } catch (\ValueError) {
                    $errors['moodColor'][] = 'moodColor is invalid.';
                }
            }
        }

        if ($hasAnyChange === false) {
            $errors['payload'][] = 'At least one of title, content or moodColor must be provided.';
        }

        if ($errors !== []) {
            throw ValidationException::fromErrors(
                message: 'Invalid poem update payload.',
                errors: $errors
            );
        }

        return new self($title, $content, $moodColor, true);
    }

    public function hasAnyChange(): bool
    {
        return $this->hasAnyChange;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function getMoodColor(): ?MoodColor
    {
        return $this->moodColor;
    }
}
