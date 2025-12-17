<?php

namespace App\Http\Request\User;

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

    /**
     * @var string[]|null
     */
    private ?array $roles;

    /**
     * @param string[]|null $roles
     */
    private function __construct(?string $email, ?string $passwordHash, ?array $roles)
    {
        $this->email = $email;
        $this->passwordHash = $passwordHash;
        $this->roles = $roles;
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

        if ($errors !== []) {
            throw ValidationException::fromErrors(
                message: 'Invalid user payload.',
                errors: $errors
            );
        }

        return new self($email, $passwordHash, $roles);
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    /**
     * @return string[]|null
     */
    public function getRoles(): ?array
    {
        return $this->roles;
    }
}
