<?php

namespace App\Http\Request\Totem;

use App\Http\Exception\ValidationException;
use App\Http\Request\JsonRequestDecoder;
use App\Http\Request\RequestPayload;
use Symfony\Component\HttpFoundation\Request;

/**
 * Parses and validates payload for creating a Totem.
 */
final class CreateTotemRequest
{
    private string $name;
    private ?string $description;
    private ?string $picture;

    private function __construct(string $name, ?string $description, ?string $picture)
    {
        $this->name = $name;
        $this->description = $description;
        $this->picture = $picture;
    }

    /**
     * Expected JSON:
     * {
     *   "name": "Phoenix",
     *   "description": "Legendary bird",
     *   "picture": "https://..."
     * }
     *
     * @throws ValidationException
     */
    public static function fromHttpRequest(Request $request): self
    {
        $payload = JsonRequestDecoder::decodeObjectOrFail($request);
        $errors = [];

        $name = RequestPayload::getTrimmedString($payload, 'name');
        if ($name === null) {
            $errors['name'][] = 'Name is required.';
        }

        $description = null;
        if (\array_key_exists('description', $payload)) {
            $description = RequestPayload::getTrimmedString($payload, 'description');
            if ($payload['description'] !== null && $description === null) {
                $errors['description'][] = 'Description cannot be empty when provided.';
            }
        }

        $picture = null;
        if (\array_key_exists('picture', $payload)) {
            $picture = RequestPayload::getTrimmedString($payload, 'picture');
            if ($payload['picture'] !== null && $picture === null) {
                $errors['picture'][] = 'Picture cannot be empty when provided.';
            }
        }

        if ($errors !== []) {
            throw ValidationException::fromErrors(
                message: 'Invalid totem payload.',
                errors: $errors
            );
        }

        /** @var string $name */
        return new self($name, $description, $picture);
    }

    public function getName(): string
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
