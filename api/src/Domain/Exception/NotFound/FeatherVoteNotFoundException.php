<?php

namespace App\Domain\Exception\NotFound;

/**
 * Thrown when a feather vote cannot be found.
 */
final class FeatherVoteNotFoundException extends NotFoundException
{
    public function __construct(string $message = 'FeatherVote not found.')
    {
        parent::__construct($message, 'FEATHER_VOTE_NOT_FOUND');
    }
}
