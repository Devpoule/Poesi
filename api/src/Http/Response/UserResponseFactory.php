<?php

namespace App\Http\Response;

use App\Domain\Entity\User;

/**
 * Builds API-friendly payloads for User entities.
 */
class UserResponseFactory
{
    /**
     * Transform a single User entity into an array payload.
     *
     * @return array<string,mixed>
     */
    public static function fromEntity(User $user): array
    {
        return [
            'id'    => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
        ];
    }

    /**
     * Transform a list of User entities into an array payload.
     *
     * @param iterable<User> $users
     *
     * @return array<int,array<string,mixed>>
     */
    public static function fromCollection(iterable $users): array
    {
        $items = [];

        foreach ($users as $user) {
            $items[] = self::fromEntity($user);
        }

        return $items;
    }
}
