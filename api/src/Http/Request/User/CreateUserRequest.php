<?php

namespace App\Http\Request\User;

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
    private string $passwordHash;

    /**
     * @var string[]
     */
    private array $roles;

    /**
     * @param string[] $roles
     */
    private function __construct(string $email, string $passwordHash, array $roles)
    {
        $this->email = $email;
        $this->passwordHash = $passwordHash;
        $this->roles = $roles;
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
        if ($passwordHash === null) {
            $errors['passwordHash'][] = 'PasswordHash is required.';
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

        if ($errors !== []) {
            throw ValidationException::fromErrors(
                message: 'Invalid user payload.',
                errors: $errors
            );
        }

        /** @var string $email */
        /** @var string $passwordHash */
        return new self($email, $passwordHash, $roles);
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }
}
