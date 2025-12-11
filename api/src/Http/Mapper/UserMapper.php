<?php

namespace App\Http\Mapper;

use App\Domain\Entity\User;

/**
 * Maps User domain entities to array structures
 * suitable for JSON API responses.
 *
 * The password hash is never exposed.
 */
class UserMapper
{
    /**
     * Transform a User entity into a flat array representation.
     *
     * @param User $user
     *
     * @return array<string, mixed>
     */
    public function toArray(User $user): array
    {
        return [
            'id'    => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
        ];
    }

    /**
     * Transform a collection of User entities into an array of arrays.
     *
     * @param iterable<User> $users
     *
     * @return array<int, array<string, mixed>>
     */
    public function toCollection(iterable $users): array
    {
        $result = [];

        foreach ($users as $user) {
            $result[] = $this->toArray($user);
        }

        return $result;
    }
}
