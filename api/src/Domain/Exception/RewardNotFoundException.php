<?php

namespace App\Domain\Exception;

use App\Http\Exception\ApiExceptionInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Thrown when a Reward entity cannot be found.
 */
class RewardNotFoundException extends \RuntimeException implements ApiExceptionInterface
{
    private string $errorCode;
    private int $httpStatus;

    public function __construct(
        string $message = 'Reward not found.',
        string $errorCode = 'REWARD_NOT_FOUND',
        int $httpStatus = Response::HTTP_NOT_FOUND
    ) {
        parent::__construct($message);

        $this->errorCode  = $errorCode;
        $this->httpStatus = $httpStatus;
    }

    /**
     * Returns the technical error code.
     */
    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    /**
     * Returns the HTTP status code for this error.
     */
    public function getHttpStatus(): int
    {
        return $this->httpStatus;
    }

    /**
     * Returns the error type for UI feedback.
     */
    public function getType(): string
    {
        return 'error';
    }
}
