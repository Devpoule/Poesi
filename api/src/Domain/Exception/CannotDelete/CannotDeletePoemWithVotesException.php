<?php

namespace App\Domain\Exception\CannotDelete;

/**
 * Thrown when trying to delete a poem that is still referenced by feather votes.
 */
class CannotDeletePoemWithVotesException extends \RuntimeException
{
}
