<?php

namespace App\Domain\Exception\CannotVote;

use App\Domain\Exception\DomainException;

/**
 * Thrown when a user tries to vote for their own poem.
 */
final class CannotVoteOwnPoemException extends DomainException
{
    public function __construct(string $message = 'Cannot vote for own poem.')
    {
        parent::__construct($message, 'VOTE_OWN_POEM_CONFLICT');
    }
}
