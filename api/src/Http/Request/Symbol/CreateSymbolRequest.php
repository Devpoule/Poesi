<?php

namespace App\Http\Request\Symbol;

use Symfony\Component\HttpFoundation\Request;

final class CreateSymbolRequest
{
    public function __construct(
        private readonly string $key,
        private readonly string $label,
        private readonly ?string $description,
        private readonly ?string $picture,
    ) {
    }

    public static function fromHttpRequest(Request $request): self
    {
        /** @var array<string, mixed> $payload */
        $payload = (array) json_decode((string) $request->getContent(), true);

        $key = isset($payload['key']) ? trim((string) $payload['key']) : '';
        $label = isset($payload['label']) ? trim((string) $payload['label']) : '';

        if ($key === '' || $label === '') {
            throw new \InvalidArgumentException('Fields "key" and "label" are required.');
        }

        $description = isset($payload['description']) ? (string) $payload['description'] : null;
        $picture = isset($payload['picture']) ? (string) $payload['picture'] : null;

        return new self($key, $label, $description, $picture);
    }

    public function getKey(): string { return $this->key; }
    public function getLabel(): string { return $this->label; }
    public function getDescription(): ?string { return $this->description; }
    public function getPicture(): ?string { return $this->picture; }
}
