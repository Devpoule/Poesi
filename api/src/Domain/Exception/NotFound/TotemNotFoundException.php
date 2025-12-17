<?php

namespace App\Domain\Exception\NotFound;

/**
 * Thrown when a totem cannot be found.
 */
final class TotemNotFoundException extends NotFoundException
{
    public function __construct(string $message = 'Totem not found.')
    {
        parent::__construct($message, 'TOTEM_NOT_FOUND');
    }
}
