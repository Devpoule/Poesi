<?php

namespace App\Http\Security;

use App\Domain\Entity\Poem;
use App\Domain\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class PoemVoter extends Voter
{
    public const EDIT = 'POEM_EDIT';
    public const PUBLISH = 'POEM_PUBLISH';
    public const DELETE = 'POEM_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::EDIT, self::PUBLISH, self::DELETE], true)) {
            return false;
        }

        return $subject instanceof Poem;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        if (!$subject instanceof Poem || !$user instanceof User) {
            return false;
        }

        $author = $subject->getAuthor();

        return $author !== null && $author->getId() === $user->getId();
    }
}
