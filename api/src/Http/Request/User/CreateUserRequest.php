<?php

namespace App\Http\Request\User;

use App\Domain\Enum\MoodColor;
use App\Http\Exception\ValidationException;
use App\Http\Request\JsonRequestDecoder;
use App\Http\Request\RequestPayload;
use Symfony\Component\HttpFoundation\Request;

/**
 * Parses and validates payload for creating a User.
 *
 * Note: passwordHash is expected to be already hashed (temporary contract).
 */
final class CreateUserRequest
{
    private string $email;
    private ?string $passwordHash;
    private ?string $password;
    private ?string $pseudo;
    private ?MoodColor $moodColor;
    private ?int $totemId;
    private ?string $totemKey;

    /**
     * @var string[]
     */
    private array $roles;

    /**
     * @param string[] $roles
     */
    private function __construct(
        string $email,
        ?string $passwordHash,
        ?string $password,
        array $roles,
        ?string $pseudo,
        ?MoodColor $moodColor,
        ?int $totemId,
        ?string $totemKey
    )
    {
        $this->email = $email;
        $this->passwordHash = $passwordHash;
        $this->password = $password;
        $this->roles = $roles;
        $this->pseudo = $pseudo;
        $this->moodColor = $moodColor;
        $this->totemId = $totemId;
        $this->totemKey = $totemKey;
    }

    /**
     * Expected JSON:
     * {
     *   "email": "user@example.com",
     *   "passwordHash": "hashed",
     *   "roles": ["ROLE_USER"]
     * }
     *
     * @throws ValidationException
     */
    public static function fromHttpRequest(Request $request): self
    {
        $payload = JsonRequestDecoder::decodeObjectOrFail($request);
        $errors = [];

        $email = RequestPayload::getTrimmedString($payload, 'email');
        if ($email === null) {
            $errors['email'][] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'][] = 'email format is invalid.';
        }

        $passwordHash = RequestPayload::getTrimmedString($payload, 'passwordHash');
        $password = RequestPayload::getTrimmedString($payload, 'password');
        if ($passwordHash === null && $password === null) {
            $errors['password'][] = 'Password or passwordHash is required.';
        }

        $roles = $payload['roles'] ?? ['ROLE_USER'];
        if (!is_array($roles)) {
            $roles = ['ROLE_USER'];
        }

        $roles = array_values(array_filter(
            $roles,
            static fn ($r) => is_string($r) && trim($r) !== ''
        ));

        if ($roles === []) {
            $roles = ['ROLE_USER'];
        }

        $pseudo = RequestPayload::getTrimmedString($payload, 'pseudo');

        $moodColor = null;
        $moodRaw = RequestPayload::getTrimmedString($payload, 'moodColor');
        if ($moodRaw !== null) {
            try {
                $moodColor = MoodColor::from($moodRaw);
            } catch (\ValueError) {
                $errors['moodColor'][] = 'MoodColor is invalid.';
            }
        }

        $totemId = RequestPayload::getPositiveInt($payload, 'totemId');
        $totemKey = RequestPayload::getTrimmedString($payload, 'totemKey');

        if ($errors !== []) {
            throw ValidationException::fromErrors(
                message: 'Invalid user payload.',
                errors: $errors
            );
        }

        /** @var string $email */
        return new self($email, $passwordHash, $password, $roles, $pseudo, $moodColor, $totemId, $totemKey);
    }

    public function getEmail(): string
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
     * @return string[]
     */
    public function getRoles(): array
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
