<?php

namespace App\Domain\Exception\CannotPublish;

use App\Domain\Exception\DomainException;

/**
 * Base class for domain publish conflict errors.
 *
 * 409 Conflict
 * References:
 * https://www.rfc-editor.org/rfc/rfc9110#name-409-conflict
 */
abstract class CannotPublishException extends DomainException
{
}
