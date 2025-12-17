<?php

namespace App\Http\Response;

use App\Domain\Entity\Author;

final class AuthorResponse
{
    private const DEFAULT_TOTEM_ID = 1;

    /**
     * @return array{
     *   id:int|null,
     *   pseudo:string,
     *   email:string,
     *   moodColor:string|null,
     *   createdAt:string,
     *   totem:array{
     *     id:int|null,
     *     name:string|null,
     *     state:string
     *   }
     * }
     */
    public function item(Author $author): array
    {
        $totem     = $author->getTotem();
        $moodColor = $author->getMoodColor();

        $totemId = $totem?->getId();
        $state   = ($totemId === self::DEFAULT_TOTEM_ID) ? 'pending' : 'chosen';

        return [
            'id'        => $author->getId(),
            'pseudo'    => $author->getPseudo(),
            'email'     => $author->getEmail(),
            'moodColor' => $moodColor?->value,
            'createdAt' => $author->getCreatedAt()->format(\DateTimeInterface::ATOM),
            'totem'     => [
                'id'    => $totemId,
                'name'  => $totem?->getName(),
                'state' => $state,
            ],
        ];
    }

    /**
     * @param iterable<Author> $authors
     *
     * @return array<int, array<string, mixed>>
     */
    public function collection(iterable $authors): array
    {
        $result = [];

        foreach ($authors as $author) {
            $result[] = $this->item($author);
        }

        return $result;
    }
}
