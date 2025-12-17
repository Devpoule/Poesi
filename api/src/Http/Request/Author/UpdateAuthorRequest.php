<?php

namespace App\Http\Request\Author;

use App\Domain\Enum\MoodColor;
use App\Http\Exception\ValidationException;
use App\Http\Request\JsonRequestDecoder;
use Symfony\Component\HttpFoundation\Request;

/**
 * Parses and validates the payload for updating an Author.
 *
 * All fields are optional; if a field is provided, it must be valid.
 */
final class UpdateAuthorRequest
{
    private ?string $pseudo;
    private ?MoodColor $moodColor;
    private ?int $totemId;

    private function __construct(?string $pseudo, ?MoodColor $moodColor, ?int $totemId)
    {
        $this->pseudo = $pseudo;
        $this->moodColor = $moodColor;
        $this->totemId = $totemId;
    }

    /**
     * @throws ValidationException
     */
    public static function fromHttpRequest(Request $request): self
    {
        $payload = JsonRequestDecoder::decodeObjectOrFail($request);
        $errors = [];

        $pseudo = null;
        if (array_key_exists('pseudo', $payload)) {
            if ($payload['pseudo'] === null) {
                $pseudo = null;
            } elseif (!is_string($payload['pseudo']) || trim($payload['pseudo']) === '') {
                $errors['pseudo'][] = 'Pseudo must be a non-empty string when provided.';
            } else {
                $pseudo = trim($payload['pseudo']);
            }
        }

        $totemId = null;
        if (array_key_exists('totemId', $payload)) {
            $totemId = UpdateAuthorRequest::normalizeInt($payload['totemId'], 'totemId', $errors);

            if ($totemId !== null && $totemId <= 0) {
                $errors['totemId'][] = 'Totem id must be greater than 0.';
            }
        }

        $moodColor = null;
        if (array_key_exists('moodColor', $payload)) {
            if ($payload['moodColor'] === null) {
                $moodColor = null;
            } elseif (!is_string($payload['moodColor']) || trim($payload['moodColor']) === '') {
                $errors['moodColor'][] = 'Mood color must be a non-empty string when provided.';
            } else {
                try {
                    $moodColor = MoodColor::from($payload['moodColor']);
                } catch (\ValueError) {
                    $errors['moodColor'][] = 'Mood color value is invalid.';
                }
            }
        }

        if (!empty($errors)) {
            throw ValidationException::fromErrors(
                message: 'Invalid author payload.',
                errors: $errors
            );
        }

        return new self($pseudo, $moodColor, $totemId);
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function getMoodColor(): ?MoodColor
    {
        return $this->moodColor;
    }

    public function getTotemId(): ?int
    {
        return $this->totemId;
    }

    /**
     * @param mixed $value
     * @param array<string, string[]> $errors
     */
    private static function normalizeInt(mixed $value, string $field, array &$errors): ?int
    {
        if ($value === null) {
            return null;
        }

        if (is_int($value)) {
            return $value;
        }

        if (is_string($value) && ctype_digit($value)) {
            return (int) $value;
        }

        $errors[$field][] = 'Value must be an integer when provided.';

        return null;
    }
}
