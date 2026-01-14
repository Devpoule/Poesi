<?php

namespace App\Domain\Exception\CannotDelete;

/**
 * Thrown when a poem cannot be deleted due to constraints.
 */
final class CannotDeletePoemWithVotesException extends CannotDeleteException
{
    public function __construct(string $message = 'Cannot delete poem: votes exist.')
    {
        parent::__construct($message, 'POEM_DELETE_CONFLICT');
    }
}
