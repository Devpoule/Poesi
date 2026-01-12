<?php

namespace App\Domain\Exception\CannotPublish;

use App\Domain\Exception\DomainException;

/**
 * Exception thrown when trying to publish a poem without an associated totem.
 * 
 * 400 Bad Request
 * References:
 * https://www.rfc-editor.org/rfc/rfc9110#name-400-bad-request
 */
final class CannotPublishWithoutTotemException extends DomainException
{
    public function __construct()
    {
        parent::__construct(
            'You must choose a totem before publishing a poem.',
            'AUTHOR_TOTEM_REQUIRED'
        );
    }
}
