<?php

namespace App\Http\Request\Relic;

use Symfony\Component\HttpFoundation\Request;

final class CreateRelicRequest
{
    public function __construct(
        private readonly string $key,
        private readonly string $label,
        private readonly string $rarity,
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
        $rarity = isset($payload['rarity']) ? trim((string) $payload['rarity']) : '';

        if ($key === '' || $label === '' || $rarity === '') {
            throw new \InvalidArgumentException('Fields "key", "label" and "rarity" are required.');
        }

        $description = isset($payload['description']) ? (string) $payload['description'] : null;
        $picture = isset($payload['picture']) ? (string) $payload['picture'] : null;

        return new self($key, $label, $rarity, $description, $picture);
    }

    public function getKey(): string { return $this->key; }
    public function getLabel(): string { return $this->label; }
    public function getRarity(): string { return $this->rarity; }
    public function getDescription(): ?string { return $this->description; }
    public function getPicture(): ?string { return $this->picture; }
}
