<?php

namespace App\Domain\Exception\Conflict;

use App\Domain\Exception\DomainException;

/**
 * Base class for domain conflict errors.
 *
 * 409 Conflict
 * References:
 * https://www.rfc-editor.org/rfc/rfc9110#name-409-conflict
 */
abstract class ConflictException extends DomainException
{
}
