<?php

namespace App\Http\Request;

use App\Http\Exception\ValidationException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Decodes and validates JSON bodies for HTTP Request DTOs.
 */
final class JsonRequestDecoder
{
    /**
     * @return array<string, mixed>
     */
    public static function decodeObjectOrFail(Request $request): array
    {
        $raw = $request->getContent();

        if (!is_string($raw) || trim($raw) === '') {
            throw ValidationException::fromErrors(
                message: 'Request body must be a valid JSON object.',
                errors: ['body' => ['Request body is empty.']],
                errorCode: 'INVALID_JSON'
            );
        }

        try {
            /** @var mixed $decoded */
            $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw ValidationException::fromErrors(
                message: 'Request body must be a valid JSON object.',
                errors: ['body' => ['Malformed JSON payload.']],
                errorCode: 'INVALID_JSON'
            );
        }

        if (!is_array($decoded)) {
            throw ValidationException::fromErrors(
                message: 'Request body must be a valid JSON object.',
                errors: ['body' => ['JSON payload must decode to an object.']],
                errorCode: 'INVALID_JSON'
            );
        }

        /** @var array<string, mixed> $decoded */
        return $decoded;
    }
}
