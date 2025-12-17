<?php

namespace App\Http\Request\Author;

use App\Domain\Enum\MoodColor;
use App\Http\Exception\ValidationException;
use App\Http\Request\JsonRequestDecoder;
use Symfony\Component\HttpFoundation\Request;

/**
 * Parses and validates payload for author creation.
 *
 * Totem selection policy:
 * - If totemId is provided => use it
 * - Else if randomTotem=true => pick a random totem (excluding default Egg)
 * - Else => use default Egg totem (id=1)
 */
final class CreateAuthorRequest
{
    private string $pseudo;
    private string $email;
    private ?int $totemId;
    private ?MoodColor $moodColor;
    private bool $randomTotem;

    private function __construct(
        string $pseudo,
        string $email,
        ?int $totemId,
        ?MoodColor $moodColor,
        bool $randomTotem
    ) {
        $this->pseudo      = $pseudo;
        $this->email       = $email;
        $this->totemId     = $totemId;
        $this->moodColor   = $moodColor;
        $this->randomTotem = $randomTotem;
    }

    /**
     * @throws ValidationException
     */
    public static function fromHttpRequest(Request $request): self
    {
        $payload = JsonRequestDecoder::decodeObjectOrFail($request);
        $errors  = [];

        $pseudo = $payload['pseudo'] ?? null;
        if (!is_string($pseudo) || trim($pseudo) === '') {
            $errors['pseudo'][] = 'Pseudo is required.';
        }

        $email = $payload['email'] ?? null;
        if (!is_string($email) || trim($email) === '') {
            $errors['email'][] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'][] = 'Email format is invalid.';
        }

        $totemId = null;
        if (\array_key_exists('totemId', $payload) && $payload['totemId'] !== null) {
            if (is_int($payload['totemId']) || ctype_digit((string) $payload['totemId'])) {
                $totemId = (int) $payload['totemId'];
                if ($totemId <= 0) {
                    $errors['totemId'][] = 'Totem id must be greater than 0.';
                }
            } else {
                $errors['totemId'][] = 'Totem id must be an integer.';
            }
        }

        $randomTotem = false;
        if (\array_key_exists('randomTotem', $payload)) {
            $raw = $payload['randomTotem'];
            if ($raw === null) {
                $randomTotem = false;
            } elseif (!is_bool($raw)) {
                $errors['randomTotem'][] = 'randomTotem must be a boolean.';
            } else {
                $randomTotem = $raw;
            }
        }

        $moodColor = null;
        if (\array_key_exists('moodColor', $payload) && $payload['moodColor'] !== null) {
            try {
                $moodColor = MoodColor::from((string) $payload['moodColor']);
            } catch (\ValueError) {
                $errors['moodColor'][] = 'Mood color value is invalid.';
            }
        }

        if ($errors !== []) {
            throw ValidationException::fromErrors(
                message: 'Invalid author payload.',
                errors: $errors
            );
        }

        return new self(
            pseudo: trim($pseudo),
            email: trim($email),
            totemId: $totemId,
            moodColor: $moodColor,
            randomTotem: $randomTotem
        );
    }

    public function getPseudo(): string
    {
        return $this->pseudo;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getTotemId(): ?int
    {
        return $this->totemId;
    }

    public function getMoodColor(): ?MoodColor
    {
        return $this->moodColor;
    }

    public function isRandomTotem(): bool
    {
        return $this->randomTotem;
    }
}
