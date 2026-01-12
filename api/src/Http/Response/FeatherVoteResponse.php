<?php

namespace App\Http\Response;

use App\Domain\Entity\FeatherVote;

final class FeatherVoteResponse
{
    /**
     * @return array{
     *   id:int|null,
     *   featherType:string|null,
     *   createdAt:string,
     *   updatedAt:string,
     *   voter:array{id:int|null,pseudo:string|null,state:string},
     *   poem:array{id:int|null,title:string|null,state:string}
     * }
     */
    public function item(FeatherVote $vote): array
    {
        $voter = $vote->getVoter();
        $poem  = $vote->getPoem();

        return [
            'id'         => $vote->getId(),
            'featherType'=> $vote->getFeatherType()?->value,
            'createdAt'  => $vote->getCreatedAt()->format(\DateTimeInterface::ATOM),
            'updatedAt'  => $vote->getUpdatedAt()->format(\DateTimeInterface::ATOM),

            'voter' => $voter !== null
                ? ['id' => $voter->getId(), 'pseudo' => $voter->getPseudo(), 'state' => 'known']
                : ['id' => null, 'pseudo' => null, 'state' => 'missing'],

            'poem' => $poem !== null
                ? ['id' => $poem->getId(), 'title' => $poem->getTitle(), 'state' => 'known']
                : ['id' => null, 'title' => null, 'state' => 'missing'],
        ];
    }

    /**
     * @param iterable<FeatherVote> $votes
     *
     * @return array<int, array<string, mixed>>
     */
    public function collection(iterable $votes): array
    {
        $result = [];
        foreach ($votes as $vote) {
            $result[] = $this->item($vote);
        }
        return $result;
    }
}
