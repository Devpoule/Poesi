<?php

namespace App\Domain\Exception\NotFound;

use App\Domain\Exception\DomainException;

/**
 * Base class for domain not found errors.
 * 
 * 404
 * References:
 * https://www.rfc-editor.org/rfc/rfc9110#name-404-not-found
 */
abstract class NotFoundException extends DomainException
{
}
