<?php

namespace App\Http\Request\Totem;

use App\Http\Exception\ValidationException;
use App\Http\Request\JsonRequestDecoder;
use App\Http\Request\RequestPayload;
use Symfony\Component\HttpFoundation\Request;

/**
 * Parses and validates payload for updating a Totem.
 *
 * All fields are optional, but at least one must be provided.
 */
final class UpdateTotemRequest
{
    private ?string $name;
    private ?string $description;
    private ?string $picture;

    private function __construct(?string $name, ?string $description, ?string $picture)
    {
        $this->name = $name;
        $this->description = $description;
        $this->picture = $picture;
    }

    /**
     * Expected JSON (at least one key):
     * {
     *   "name": "New name",
     *   "description": "New description",
     *   "picture": "https://..."
     * }
     *
     * @throws ValidationException
     */
    public static function fromHttpRequest(Request $request): self
    {
        $payload = JsonRequestDecoder::decodeObjectOrFail($request);
        $errors = [];
        $hasAny = false;

        $name = null;
        if (\array_key_exists('name', $payload)) {
            $hasAny = true;
            $name = RequestPayload::getTrimmedString($payload, 'name');
            if ($name === null) {
                $errors['name'][] = 'name cannot be empty when provided.';
            }
        }

        $description = null;
        if (\array_key_exists('description', $payload)) {
            $hasAny = true;
            $description = RequestPayload::getTrimmedString($payload, 'description');
            if ($description === null) {
                $errors['description'][] = 'description cannot be empty when provided.';
            }
        }

        $picture = null;
        if (\array_key_exists('picture', $payload)) {
            $hasAny = true;
            $picture = RequestPayload::getTrimmedString($payload, 'picture');
            if ($picture === null) {
                $errors['picture'][] = 'picture cannot be empty when provided.';
            }
        }

        if ($hasAny === false) {
            $errors['payload'][] = 'At least one field must be provided.';
        }

        if ($errors !== []) {
            throw ValidationException::fromErrors(
                message: 'Invalid totem update payload.',
                errors: $errors
            );
        }

        return new self($name, $description, $picture);
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }
}
