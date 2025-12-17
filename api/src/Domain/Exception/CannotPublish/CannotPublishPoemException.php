<?php

namespace App\Domain\Exception\CannotPublish;

/**
 * Thrown when a poem cannot be published due to constraints.
 */
final class CannotPublishPoemException extends CannotPublishException
{
    public function __construct(string $message = 'Cannot publish poem.')
    {
        parent::__construct($message, 'POEM_PUBLISH_CONFLICT');
    }
}
