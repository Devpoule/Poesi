<?php

namespace App\Http\Security;

use App\Domain\Entity\User;
use App\Domain\Entity\UserReward;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserRewardVoter extends Voter
{
    public const LIST = 'USER_REWARD_LIST';
    public const DELETE = 'USER_REWARD_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::LIST, self::DELETE], true)) {
            return false;
        }

        if ($attribute === self::LIST) {
            return is_int($subject);
        }

        return $subject instanceof UserReward;
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

        if (!$subject instanceof UserReward || !$user instanceof User) {
            return false;
        }

        $owner = $subject->getUser();

        return $owner !== null && $owner->getId() === $user->getId();
    }
}
