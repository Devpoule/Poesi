<?php

namespace App\Http\Response;

use App\Domain\Entity\User;

final class UserResponse
{
    /**
     * @return array{id:int|null, email:string, roles:string[]}
     */
    public function item(User $user): array
    {
        return [
            'id'    => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
        ];
    }

    /**
     * @param iterable<User> $users
     *
     * @return array<int, array<string, mixed>>
     */
    public function collection(iterable $users): array
    {
        $result = [];
        foreach ($users as $user) {
            $result[] = $this->item($user);
        }
        return $result;
    }
}
