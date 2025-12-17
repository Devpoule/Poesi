<?php

namespace App\Http\Request\Author;

use App\Http\Exception\ValidationException;
use App\Http\Request\JsonRequestDecoder;
use Symfony\Component\HttpFoundation\Request;

final class HatchAuthorRequest
{
    private const DEFAULT_TOTEM_ID = 1;

    private int $totemId;

    private function __construct(int $totemId)
    {
        $this->totemId = $totemId;
    }

    public static function fromHttpRequest(Request $request): self
    {
        $payload = JsonRequestDecoder::decodeObjectOrFail($request);
        $errors  = [];

        $raw = $payload['totemId'] ?? null;

        if (!is_int($raw) && !(is_string($raw) && ctype_digit($raw))) {
            $errors['totemId'][] = 'totemId must be a positive integer.';
        }

        $totemId = (int) $raw;

        if ($totemId <= 0) {
            $errors['totemId'][] = 'totemId must be greater than 0.';
        }

        if ($totemId === self::DEFAULT_TOTEM_ID) {
            $errors['totemId'][] = 'totemId cannot be the default Egg.';
        }

        if ($errors !== []) {
            throw ValidationException::fromErrors(
                message: 'Invalid hatch payload.',
                errors: $errors
            );
        }

        return new self($totemId);
    }

    public function getTotemId(): int
    {
        return $this->totemId;
    }
}
