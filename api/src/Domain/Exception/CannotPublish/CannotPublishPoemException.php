<?php

namespace App\Domain\Exception;

/**
 * Exception thrown when trying to publish a poem without an associated totem.
 * 
 * 400 Bad Request
 * References:
 * https://www.rfc-editor.org/rfc/rfc9110#name-400-bad-request
 */
final class CannotPublishPoemException extends DomainException
{
    public function __construct(string $message = 'Poem cannot be published until a totem is chosen.')
    {
        parent::__construct($message, 'POEM_PUBLISH_TOTEM_REQUIRED');
    }
}
