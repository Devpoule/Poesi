<?php

namespace App\Domain\Exception\CannotDelete;

/**
 * Thrown when a user cannot be deleted due to existing links.
 */
final class CannotDeleteUserException extends CannotDeleteException
{
    public function __construct(string $message = 'Cannot delete user.')
    {
        parent::__construct($message, 'USER_DELETE_CONFLICT');
    }
}
