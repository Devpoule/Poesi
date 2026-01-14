<?php

namespace App\Domain\Exception\Conflict;

/**
 * Thrown when trying to create/update a user with an email already in use.
 */
final class EmailAlreadyUsedException extends ConflictException
{
    public function __construct(string $email)
    {
        parent::__construct(
            sprintf('Email "%s" is already used.', $email),
            'EMAIL_ALREADY_USED'
        );
    }
}
