<?php

namespace App\Domain\Exception\CannotUpdate;

use App\Domain\Exception\DomainException;

/**
 * Thrown when a poem cannot be updated due to constraints.
 */
final class CannotUpdatePoemException extends DomainException
{
    public function __construct(string $message = 'Cannot update poem.')
    {
        parent::__construct($message, 'POEM_UPDATE_CONFLICT');
    }
}
