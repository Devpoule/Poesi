<?php

namespace App\Http\Request\Mood;

use Symfony\Component\HttpFoundation\Request;

final class CreateMoodRequest
{
    public function __construct(
        private readonly string $key,
        private readonly string $label,
        private readonly ?string $description,
        private readonly ?string $icon,
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
        $icon = isset($payload['icon']) ? (string) $payload['icon'] : null;

        return new self($key, $label, $description, $icon);
    }

    public function getKey(): string { return $this->key; }
    public function getLabel(): string { return $this->label; }
    public function getDescription(): ?string { return $this->description; }
    public function getIcon(): ?string { return $this->icon; }
}
