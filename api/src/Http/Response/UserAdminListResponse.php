<?php

namespace App\Http\Response;

final class UserAdminListResponse
{
    /**
     * @param array{
     *   id:int,
     *   email:string,
     *   pseudo:string|null,
     *   roles:string[],
     *   createdAt:\DateTimeInterface,
     *   lockedAt:\DateTimeInterface|null,
     *   failedLoginAttempts:int
     * } $row
     *
     * @return array{
     *   id:int,
     *   email:string,
     *   pseudo:string|null,
     *   roles:string[],
     *   createdAt:string|null,
     *   lockedAt:string|null,
     *   failedLoginAttempts:int
     * }
     */
    private function item(array $row): array
    {
        $createdAt = $row['createdAt'] ?? null;
        $lockedAt = $row['lockedAt'] ?? null;

        return [
            'id' => (int) $row['id'],
            'email' => (string) $row['email'],
            'pseudo' => $row['pseudo'] ?? null,
            'roles' => is_array($row['roles'] ?? null) ? $row['roles'] : [],
            'createdAt' => $createdAt instanceof \DateTimeInterface
                ? $createdAt->format(\DateTimeInterface::ATOM)
                : null,
            'lockedAt' => $lockedAt instanceof \DateTimeInterface
                ? $lockedAt->format(\DateTimeInterface::ATOM)
                : null,
            'failedLoginAttempts' => (int) ($row['failedLoginAttempts'] ?? 0),
        ];
    }

    /**
     * @param list<array<string, mixed>> $rows
     *
     * @return array<int, array<string, mixed>>
     */
    public function collection(array $rows): array
    {
        $result = [];
        foreach ($rows as $row) {
            $result[] = $this->item($row);
        }

        return $result;
    }
}
