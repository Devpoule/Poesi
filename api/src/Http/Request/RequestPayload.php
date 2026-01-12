<?php

namespace App\Http\Request;

/**
 * Small helper to normalize common payload types for HTTP DTOs.
 * It centralizes "int from string", trimming, etc. to avoid duplication.
 */
final class RequestPayload
{
    /**
     * @param array<string, mixed> $payload
     */
    public static function getTrimmedString(array $payload, string $key): ?string
    {
        if (!\array_key_exists($key, $payload) || $payload[$key] === null) {
            return null;
        }

        if (!is_string($payload[$key])) {
            return null;
        }

        $value = trim($payload[$key]);

        return $value === '' ? null : $value;
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function getPositiveInt(array $payload, string $key): ?int
    {
        if (!\array_key_exists($key, $payload) || $payload[$key] === null) {
            return null;
        }

        $raw = $payload[$key];

        if (is_int($raw)) {
            return $raw > 0 ? $raw : null;
        }

        if (is_string($raw) && ctype_digit($raw)) {
            $value = (int) $raw;

            return $value > 0 ? $value : null;
        }

        return null;
    }
}
