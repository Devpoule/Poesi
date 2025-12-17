<?php

namespace App\Domain\Exception\NotFound;

/**
 * Thrown when an author-reward link cannot be found.
 */
final class AuthorRewardNotFoundException extends NotFoundException
{
    public function __construct(string $message = 'AuthorReward not found.')
    {
        parent::__construct($message, 'AUTHOR_REWARD_NOT_FOUND');
    }
}
