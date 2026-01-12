<?php

namespace App\Domain\Exception\NotFound;

/**
 * Thrown when a poem cannot be found.
 */
final class PoemNotFoundException extends NotFoundException
{
    public function __construct(string $message = 'Poem not found.')
    {
        parent::__construct($message, 'POEM_NOT_FOUND');
    }
}
