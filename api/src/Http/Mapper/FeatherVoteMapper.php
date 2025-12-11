<?php

namespace App\Http\Mapper;

use App\Domain\Entity\FeatherVote;

/**
 * Maps FeatherVote domain entities to array structures
 * for JSON API responses.
 */
class FeatherVoteMapper
{
    /**
     * Transform a FeatherVote entity into a flat array representation.
     *
     * @param FeatherVote $vote
     *
     * @return array<string, mixed>
     */
    public function toArray(FeatherVote $vote): array
    {
        $poem   = $vote->getPoem();
        $voter  = $vote->getVoter();

        return [
            'id'          => $vote->getId(),
            'featherType' => $vote->getFeatherType()->value,
            'createdAt'   => $vote->getCreatedAt()->format(\DATE_ATOM),
            'poem'        => $poem !== null ? [
                'id'    => $poem->getId(),
                'title' => $poem->getTitle(),
            ] : null,
            'voter'       => $voter !== null ? [
                'id'     => $voter->getId(),
                'pseudo' => $voter->getPseudo(),
            ] : null,
        ];
    }

    /**
     * Transform a collection of FeatherVote entities into an array of arrays.
     *
     * @param iterable<FeatherVote> $votes
     *
     * @return array<int, array<string, mixed>>
     */
    public function toCollection(iterable $votes): array
    {
        $result = [];

        foreach ($votes as $vote) {
            $result[] = $this->toArray($vote);
        }

        return $result;
    }
}
