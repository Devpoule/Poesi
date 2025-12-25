<?php

namespace App\Http\Request\Mood;

use Symfony\Component\HttpFoundation\Request;

final class UpdateMoodRequest
{
    public function __construct(
        private readonly ?string $key,
        private readonly ?string $label,
        private readonly ?string $description,
        private readonly ?string $icon,
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
        $icon = array_key_exists('icon', $payload) ? (string) $payload['icon'] : null;

        return new self($key, $label, $description, $icon);
    }

    public function getKey(): ?string { return $this->key; }
    public function getLabel(): ?string { return $this->label; }
    public function getDescription(): ?string { return $this->description; }
    public function getIcon(): ?string { return $this->icon; }
}
