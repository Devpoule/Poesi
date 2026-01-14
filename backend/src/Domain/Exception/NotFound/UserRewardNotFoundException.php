<?php

namespace App\Domain\Exception\NotFound;

/**
 * Thrown when a user-reward link cannot be found.
 */
final class UserRewardNotFoundException extends NotFoundException
{
    public function __construct(string $message = 'UserReward not found.')
    {
        parent::__construct($message, 'USER_REWARD_NOT_FOUND');
    }
}
