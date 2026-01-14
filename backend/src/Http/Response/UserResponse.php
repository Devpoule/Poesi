<?php

namespace App\Http\Response;

use App\Domain\Entity\User;
use App\Domain\Entity\UserRelic;
use App\Domain\Lore\LoreCatalog;

final class UserResponse
{
    public function __construct(
        private readonly LoreCatalog $loreCatalog,
    ) {
    }

    /**
     * @return array{
     *   id:int|null,
     *   email:string,
     *   pseudo:string|null,
     *   totemId:int|null,
     *   moodColor:string,
     *   roles:string[],
     *   relics:list<array{
     *     key:string,
     *     label:string,
     *     description:string,
     *     picture:string,
     *     rarity:string,
     *     grantedAt:string,
     *     reason:string|null
     *   }>
     * }
     */
    public function item(User $user): array
    {
        return [
            'id'        => $user->getId(),
            'email'     => $user->getEmail(),
            'pseudo'    => $user->getPseudo(),
            'totemId'   => $user->getTotem()?->getId(),
            'moodColor' => $user->getMoodColor()->value,
            'roles'     => $user->getRoles(),
            'relics'    => $this->mapRelics($user),
        ];
    }

    /**
     * @return list<array{
     *   key:string,
     *   label:string,
     *   description:string,
     *   picture:string,
     *   rarity:string,
     *   grantedAt:string,
     *   reason:string|null
     * }>
     */
    private function mapRelics(User $user): array
    {
        $result = [];

        foreach ($user->getRelics() as $userRelic) {
            if (!$userRelic instanceof UserRelic) {
                continue;
            }

            $key = $userRelic->getRelicKey();
            $lore = $this->loreCatalog->getRelic($key);

            $result[] = [
                'key'        => $key,
                'label'      => (string) ($lore['label'] ?? $key),
                'description'=> (string) ($lore['description'] ?? ''),
                'picture'    => (string) ($lore['picture'] ?? ''),
                'rarity'     => (string) ($lore['rarity'] ?? 'unknown'),
                'grantedAt'  => $userRelic->getGrantedAt()->format(\DateTimeInterface::ATOM),
                'reason'     => $userRelic->getReason(),
            ];
        }

        return $result;
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
