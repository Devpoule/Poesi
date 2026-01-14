<?php

namespace App\Http\Request\User;

use App\Domain\Enum\MoodColor;
use App\Http\Exception\ValidationException;
use App\Http\Request\JsonRequestDecoder;
use App\Http\Request\RequestPayload;
use Symfony\Component\HttpFoundation\Request;

/**
 * Parses and validates payload for updating a User.
 *
 * All fields are optional.
 */
final class UpdateUserRequest
{
    private ?string $email;
    private ?string $passwordHash;
    private ?string $password;
    private ?string $pseudo;
    private ?MoodColor $moodColor;
    private ?int $totemId;
    private ?string $totemKey;

    /**
     * @var string[]|null
     */
    private ?array $roles;

    /**
     * @param string[]|null $roles
     */
    private function __construct(
        ?string $email,
        ?string $passwordHash,
        ?array $roles,
        ?string $pseudo,
        ?MoodColor $moodColor,
        ?int $totemId,
        ?string $totemKey,
        ?string $password
    )
    {
        $this->email = $email;
        $this->passwordHash = $passwordHash;
        $this->roles = $roles;
        $this->pseudo = $pseudo;
        $this->moodColor = $moodColor;
        $this->totemId = $totemId;
        $this->totemKey = $totemKey;
        $this->password = $password;
    }

    /**
     * Expected JSON:
     * {
     *   "email": "new@example.com",
     *   "passwordHash": "new_hashed",
     *   "roles": ["ROLE_USER", "ROLE_ADMIN"]
     * }
     *
     * @throws ValidationException
     */
    public static function fromHttpRequest(Request $request): self
    {
        $payload = JsonRequestDecoder::decodeObjectOrFail($request);
        $errors = [];

        $email = null;
        if (\array_key_exists('email', $payload)) {
            if ($payload['email'] === null) {
                $email = null;
            } else {
                $email = RequestPayload::getTrimmedString($payload, 'email');

                if ($email === null) {
                    $errors['email'][] = 'email cannot be empty when provided.';
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors['email'][] = 'email format is invalid.';
                }
            }
        }

        $passwordHash = null;
        if (\array_key_exists('passwordHash', $payload)) {
            if ($payload['passwordHash'] === null) {
                $passwordHash = null;
            } else {
                $passwordHash = RequestPayload::getTrimmedString($payload, 'passwordHash');
                if ($passwordHash === null) {
                    $errors['passwordHash'][] = 'passwordHash cannot be empty when provided.';
                }
            }
        }

        $password = null;
        if (\array_key_exists('password', $payload)) {
            if ($payload['password'] === null) {
                $password = null;
            } else {
                $password = RequestPayload::getTrimmedString($payload, 'password');
                if ($password === null) {
                    $errors['password'][] = 'password cannot be empty when provided.';
                }
            }
        }

        $roles = null;
        if (\array_key_exists('roles', $payload)) {
            if ($payload['roles'] === null) {
                $roles = null;
            } elseif (!is_array($payload['roles'])) {
                $errors['roles'][] = 'roles must be an array when provided.';
            } else {
                $roles = array_values(array_filter(
                    $payload['roles'],
                    static fn ($r) => is_string($r) && trim($r) !== ''
                ));
            }
        }

        $pseudo = null;
        if (\array_key_exists('pseudo', $payload)) {
            if ($payload['pseudo'] === null) {
                $pseudo = null;
            } else {
                $pseudo = RequestPayload::getTrimmedString($payload, 'pseudo');
                if ($pseudo === null) {
                    $errors['pseudo'][] = 'pseudo cannot be empty when provided.';
                }
            }
        }

        $moodColor = null;
        if (\array_key_exists('moodColor', $payload)) {
            if ($payload['moodColor'] === null) {
                $moodColor = null;
            } else {
                $moodRaw = RequestPayload::getTrimmedString($payload, 'moodColor');
                if ($moodRaw === null) {
                    $errors['moodColor'][] = 'moodColor cannot be empty when provided.';
                } else {
                    try {
                        $moodColor = MoodColor::from($moodRaw);
                    } catch (\ValueError) {
                        $errors['moodColor'][] = 'moodColor is invalid.';
                    }
                }
            }
        }

        $totemId = null;
        if (\array_key_exists('totemId', $payload)) {
            if ($payload['totemId'] === null) {
                $totemId = null;
            } else {
                $totemId = RequestPayload::getPositiveInt($payload, 'totemId');
                if ($totemId === null) {
                    $errors['totemId'][] = 'totemId must be a positive integer when provided.';
                }
            }
        }

        $totemKey = null;
        if (\array_key_exists('totemKey', $payload)) {
            if ($payload['totemKey'] === null) {
                $totemKey = null;
            } else {
                $totemKey = RequestPayload::getTrimmedString($payload, 'totemKey');
                if ($totemKey === null) {
                    $errors['totemKey'][] = 'totemKey cannot be empty when provided.';
                }
            }
        }

        if ($errors !== []) {
            throw ValidationException::fromErrors(
                message: 'Invalid user payload.',
                errors: $errors
            );
        }

        return new self($email, $passwordHash, $roles, $pseudo, $moodColor, $totemId, $totemKey, $password);
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @return string[]|null
     */
    public function getRoles(): ?array
    {
        return $this->roles;
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

    public function getTotemKey(): ?string
    {
        return $this->totemKey;
    }
}
