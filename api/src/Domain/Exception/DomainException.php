<?php

namespace App\Domain\Exception;

/**
 * Base class for domain-level exceptions (HTTP-agnostic).
 */
abstract class DomainException extends \RuntimeException
{
    public function __construct(
        string $message,
        private readonly string $errorCode
    ) {
        parent::__construct($message);
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }
}
