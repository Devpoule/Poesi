<?php

namespace App\Domain\Exception\NotFound;

use App\Http\Exception\ApiExceptionInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Thrown when a reward cannot be found.
 */
class RewardNotFoundException extends \RuntimeException implements ApiExceptionInterface
{
    public function __construct(
        string $message = 'Reward not found.',
        private readonly string $errorCode = 'REWARD_NOT_FOUND'
    ) {
        parent::__construct($message);
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getHttpStatus(): int
    {
        return Response::HTTP_NOT_FOUND;
    }

    public function getType(): string
    {
        return 'error';
    }
}
