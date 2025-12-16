<?php

namespace App\Domain\Exception\CannotDelete;

/**
 * Thrown when an Author cannot be deleted due to existing relations
 * (e.g. poems, votes, rewards).
 *
 * Domain exception: the HTTP layer will map it to 409 Conflict.
 */
final class CannotDeleteAuthorException extends \RuntimeException
{
}