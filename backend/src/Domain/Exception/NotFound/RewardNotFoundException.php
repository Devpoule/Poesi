<?php

namespace App\Domain\Exception\NotFound;

/**
 * Thrown when a reward cannot be found.
 */
final class RewardNotFoundException extends NotFoundException
{
    public function __construct(string $message = 'Reward not found.')
    {
        parent::__construct($message, 'REWARD_NOT_FOUND');
    }
}
