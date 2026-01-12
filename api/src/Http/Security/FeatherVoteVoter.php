<?php

namespace App\Http\Security;

use App\Domain\Entity\FeatherVote;
use App\Domain\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class FeatherVoteVoter extends Voter
{
    public const VIEW = 'VOTE_VIEW';
    public const DELETE = 'VOTE_DELETE';
    public const LIST = 'VOTE_LIST';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::VIEW, self::DELETE, self::LIST], true)) {
            return false;
        }

        if ($attribute === self::LIST) {
            return is_int($subject);
        }

        return $subject instanceof FeatherVote;
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

        if ($attribute === self::LIST) {
            if (!$user instanceof User) {
                return false;
            }
            return $user->getId() === $subject;
        }

        if (!$subject instanceof FeatherVote || !$user instanceof User) {
            return false;
        }

        $voter = $subject->getVoter();

        return $voter !== null && $voter->getId() === $user->getId();
    }
}
