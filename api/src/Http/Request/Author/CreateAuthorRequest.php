<?php

namespace App\Http\Request\Author;

use App\Domain\Enum\MoodColor;
use App\Http\Exception\ValidationException;
use App\Http\Request\JsonRequestDecoder;
use Symfony\Component\HttpFoundation\Request;

/**
 * Request DTO responsible for parsing and validating payload
 * for author creation endpoint.
 *
 * This class belongs to the HTTP layer:
 * - it validates input shape and formats
 * - it converts primitive strings into domain-friendly types (MoodColor)
 * - it throws ValidationException containing field-level errors
 *
 * It does NOT create entities and does NOT call repositories/services.
 */
final class CreateAuthorRequest
{
    /**
     * @var string
     */
    private string $pseudo;

    /**
     * @var string
     */
    private string $email;

    /**
     * @var int|null
     */
    private ?int $totemId;

    /**
     * @var MoodColor|null
     */
    private ?MoodColor $moodColor;

    /**
     * @param string        $pseudo
     * @param string        $email
     * @param int|null      $totemId
     * @param MoodColor|null $moodColor
     */
    private function __construct(
        string $pseudo,
        string $email,
        ?int $totemId,
        ?MoodColor $moodColor
    ) {
        $this->pseudo    = $pseudo;
        $this->email     = $email;
        $this->totemId   = $totemId;
        $this->moodColor = $moodColor;
    }

    /**
     * Build the request DTO from Symfony HTTP Request.
     *
     * Expected JSON payload:
     * {
     *   "pseudo": "Tito",
     *   "email": "tito@example.com",
     *   "totemId": 1,            // optional
     *   "moodColor": "blue"      // optional (MoodColor enum value)
     * }
     *
     * @param Request $request
     *
     * @return self
     *
     * @throws ValidationException When JSON is invalid or required fields are missing/invalid.
     */
    public static function fromHttpRequest(Request $request): self
    {
        $payload = JsonRequestDecoder::decodeObjectOrFail($request);
        $errors  = [];

        // Required: pseudo
        $pseudo = $payload['pseudo'] ?? null;
        if (!is_string($pseudo) || trim($pseudo) === '') {
            $errors['pseudo'][] = 'Pseudo is required.';
        }

        // Required: email
        $email = $payload['email'] ?? null;
        if (!is_string($email) || trim($email) === '') {
            $errors['email'][] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'][] = 'Email format is invalid.';
        }

        // Optional: totemId
        $totemId = null;
        if (array_key_exists('totemId', $payload) && $payload['totemId'] !== null) {
            if (is_int($payload['totemId'])) {
                $totemId = $payload['totemId'];
            } elseif (is_string($payload['totemId']) && ctype_digit($payload['totemId'])) {
                $totemId = (int) $payload['totemId'];
            } else {
                $errors['totemId'][] = 'Totem id must be an integer.';
            }

            if ($totemId !== null && $totemId <= 0) {
                $errors['totemId'][] = 'Totem id must be greater than 0.';
            }
        }

        // Optional: moodColor
        $moodColor = null;
        if (array_key_exists('moodColor', $payload) && $payload['moodColor'] !== null) {
            if (!is_string($payload['moodColor']) || trim($payload['moodColor']) === '') {
                $errors['moodColor'][] = 'Mood color must be a non-empty string.';
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

        return new self(
            pseudo: trim($pseudo),
            email: trim($email),
            totemId: $totemId,
            moodColor: $moodColor
        );
    }

    /**
     * Decode JSON request body and throw ValidationException on failure.
     *
     * @param Request $request
     *
     * @return array<string, mixed>
     *
     * @throws ValidationException
     */
    private static function decodeJsonOrFail(Request $request): array
    {
        $raw = $request->getContent();

        if (!is_string($raw) || trim($raw) === '') {
            throw ValidationException::fromErrors(
                message: 'Request body must be a valid JSON object.',
                errors: ['body' => ['Request body is empty.']]
            );
        }

        try {
            /** @var mixed $decoded */
            $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw ValidationException::fromErrors(
                message: 'Request body must be a valid JSON object.',
                errors: ['body' => ['Malformed JSON payload.']]
            );
        }

        if (!is_array($decoded)) {
            throw ValidationException::fromErrors(
                message: 'Request body must be a valid JSON object.',
                errors: ['body' => ['JSON payload must decode to an object.']]
            );
        }

        /** @var array<string, mixed> $decoded */
        return $decoded;
    }

    /**
     * @return string
     */
    public function getPseudo(): string
    {
        return $this->pseudo;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return int|null
     */
    public function getTotemId(): ?int
    {
        return $this->totemId;
    }

    /**
     * @return MoodColor|null
     */
    public function getMoodColor(): ?MoodColor
    {
        return $this->moodColor;
    }
}
