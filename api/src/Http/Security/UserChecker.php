<?php

namespace App\Http\Security;

use App\Domain\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if ($user instanceof User && $user->isLocked()) {
            throw new CustomUserMessageAccountStatusException('Account is locked. Please contact support.');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
    }
}
