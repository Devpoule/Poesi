<?php

namespace App\Domain\Exception\CannotDelete;

/**
 * Thrown when an author cannot be deleted due to constraints.
 */
final class CannotDeleteAuthorException extends CannotDeleteException
{
    public function __construct(string $message = 'Cannot delete author.')
    {
        parent::__construct($message, 'AUTHOR_DELETE_CONFLICT');
    }
}
