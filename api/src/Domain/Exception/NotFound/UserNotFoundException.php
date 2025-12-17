<?php

namespace App\Domain\Exception\NotFound;

/**
 * Thrown when a user cannot be found.
 */
final class UserNotFoundException extends NotFoundException
{
    public function __construct(string $message = 'User not found.')
    {
        parent::__construct($message, 'USER_NOT_FOUND');
    }
}
