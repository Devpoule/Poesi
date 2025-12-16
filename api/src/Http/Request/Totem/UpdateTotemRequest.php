<?php

namespace App\Http\Request\Totem;

use App\Http\Exception\ValidationException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Validates and normalizes the payload for updating a Totem.
 *
 * Expected JSON body (all optional, but at least one must be provided):
 * {
 *   "name": "New name",
 *   "description": "New description",
 *   "picture": "https://..."
 * }
 */
final class UpdateTotemRequest
{
    /**
     * @var string|null
     */
    private ?string $name;

    /**
     * @var string|null
     */
    private ?string $description;

    /**
     * @var string|null
     */
    private ?string $picture;

    /**
     * @var bool
     */
    private bool $hasAnyChange;

    private function __construct(?string $name, ?string $description, ?string $picture, bool $hasAnyChange)
    {
        $this->name = $name;
        $this->description = $description;
        $this->picture = $picture;
        $this->hasAnyChange = $hasAnyChange;
    }

    /**
     * Builds an UpdateTotemRequest from an HTTP request.
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
        $hasAnyChange = false;

        $name = null;
        if (\array_key_exists('name', $payload)) {
            $hasAnyChange = true;

            $raw = $payload['name'];
            if ($raw === null || trim((string) $raw) === '') {
                $errors['name'][] = 'name cannot be empty when provided.';
            } else {
                $name = trim((string) $raw);
            }
        }

        $description = null;
        if (\array_key_exists('description', $payload)) {
            $hasAnyChange = true;

            $raw = $payload['description'];
            if ($raw === null || trim((string) $raw) === '') {
                $errors['description'][] = 'description cannot be empty when provided.';
            } else {
                $description = trim((string) $raw);
            }
        }

        $picture = null;
        if (\array_key_exists('picture', $payload)) {
            $hasAnyChange = true;

            $raw = $payload['picture'];
            if ($raw === null || trim((string) $raw) === '') {
                $errors['picture'][] = 'picture cannot be empty when provided.';
            } else {
                $picture = trim((string) $raw);
            }
        }

        if ($hasAnyChange === false) {
            $errors['payload'][] = 'At least one of name, description or picture must be provided.';
        }

        if (!empty($errors)) {
            throw ValidationException::fromErrors(
                message: 'Invalid totem update payload.',
                errors: $errors
            );
        }

        return new self(
            name: $name,
            description: $description,
            picture: $picture,
            hasAnyChange: $hasAnyChange
        );
    }

    /**
     * @return bool
     */
    public function hasAnyChange(): bool
    {
        return $this->hasAnyChange;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
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
