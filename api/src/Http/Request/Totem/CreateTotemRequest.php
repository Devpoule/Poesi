<?php

namespace App\Http\Request\Totem;

use App\Http\Exception\ValidationException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Validates and normalizes the payload for creating a Totem.
 *
 * Expected JSON body:
 * {
 *   "name": "Phoenix",
 *   "description": "Legendary bird",
 *   "picture": "https://cdn.example.com/totems/phoenix.png"
 * }
 */
final class CreateTotemRequest
{
    /**
     * @var string
     */
    private string $name;

    /**
     * @var string|null
     */
    private ?string $description;

    /**
     * @var string|null
     */
    private ?string $picture;

    private function __construct(string $name, ?string $description, ?string $picture)
    {
        $this->name = $name;
        $this->description = $description;
        $this->picture = $picture;
    }

    /**
     * Builds a CreateTotemRequest from an HTTP request.
     *
     * @param Request $request
     *
     * @throws ValidationException
     */
    public static function fromHttpRequest(Request $request): self
    {
        $payload = json_decode($request->getContent(), true);

        if (!is_array($payload)) {
            throw ValidationException::fromErrors(
                message: 'Invalid JSON payload.',
                errors: ['body' => ['Request body must be valid JSON.']]
            );
        }

        $errors = [];

        $name = $payload['name'] ?? null;
        $description = array_key_exists('description', $payload) ? $payload['description'] : null;
        $picture = array_key_exists('picture', $payload) ? $payload['picture'] : null;

        if ($name === null || trim((string) $name) === '') {
            $errors['name'][] = 'name is required.';
        }

        if ($description !== null && trim((string) $description) === '') {
            $errors['description'][] = 'description cannot be empty when provided.';
        }

        if ($picture !== null && trim((string) $picture) === '') {
            $errors['picture'][] = 'picture cannot be empty when provided.';
        }

        if (!empty($errors)) {
            throw ValidationException::fromErrors(
                message: 'Invalid totem payload.',
                errors: $errors
            );
        }

        return new self(
            name: trim((string) $name),
            description: $description !== null ? trim((string) $description) : null,
            picture: $picture !== null ? trim((string) $picture) : null
        );
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return string|null
     */
    public function getPicture(): ?string
    {
        return $this->picture;
    }
}
