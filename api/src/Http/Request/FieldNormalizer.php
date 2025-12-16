<?php

namespace App\Http\Request;

/**
 * Small helper for normalizing primitive JSON fields.
 */
final class FieldNormalizer
{
    public static function toNullablePositiveInt(mixed $value, string $field, array &$errors): ?int
    {
        if ($value === null) {
            return null;
        }

        if (is_int($value)) {
            $int = $value;
        } elseif (is_string($value) && ctype_digit($value)) {
            $int = (int) $value;
        } else {
            $errors[$field][] = 'Value must be an integer when provided.';
            return null;
        }

        if ($int <= 0) {
            $errors[$field][] = 'Value must be greater than 0.';
            return null;
        }

        return $int;
    }
}
