<?php

namespace App\Http\Request\Symbol;

use Symfony\Component\HttpFoundation\Request;

final class UpdateSymbolRequest
{
    public function __construct(
        private readonly ?string $key,
        private readonly ?string $label,
        private readonly ?string $description,
        private readonly ?string $picture,
    ) {
    }

    public static function fromHttpRequest(Request $request): self
    {
        /** @var array<string, mixed> $payload */
        $payload = (array) json_decode((string) $request->getContent(), true);

        $key = array_key_exists('key', $payload) ? trim((string) $payload['key']) : null;
        $label = array_key_exists('label', $payload) ? trim((string) $payload['label']) : null;

        if ($key !== null && $key === '') {
            throw new \InvalidArgumentException('Field "key" cannot be empty.');
        }
        if ($label !== null && $label === '') {
            throw new \InvalidArgumentException('Field "label" cannot be empty.');
        }

        $description = array_key_exists('description', $payload) ? (string) $payload['description'] : null;
        $picture = array_key_exists('picture', $payload) ? (string) $payload['picture'] : null;

        return new self($key, $label, $description, $picture);
    }

    public function getKey(): ?string { return $this->key; }
    public function getLabel(): ?string { return $this->label; }
    public function getDescription(): ?string { return $this->description; }
    public function getPicture(): ?string { return $this->picture; }
}
