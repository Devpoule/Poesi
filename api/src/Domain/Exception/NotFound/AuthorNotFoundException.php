<?php

namespace App\Domain\Exception\NotFound;

/**
 * Thrown when an author cannot be found.
 */
final class AuthorNotFoundException extends NotFoundException
{
    public function __construct(string $message = 'Author not found.')
    {
        parent::__construct($message, 'AUTHOR_NOT_FOUND');
    }
}
