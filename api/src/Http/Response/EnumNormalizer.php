<?php

namespace App\Http\Response;

final class EnumNormalizer
{
    public static function toString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof \BackedEnum) {
            return (string) $value->value;
        }

        if (is_string($value) || is_int($value)) {
            return (string) $value;
        }

        return null;
    }
}
