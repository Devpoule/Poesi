<?php

namespace App\Http\Response;

use App\Domain\Entity\FeatherVote;

/**
 * Builds API-friendly payloads for FeatherVote entities.
 */
class FeatherVoteResponseFactory
{
    /**
     * Transform a single FeatherVote entity into an array payload.
     *
     * @return array<string,mixed>
     */
    public static function fromEntity(FeatherVote $vote): array
    {
        return [
            'id'         => $vote->getId(),
            'featherType'=> $vote->getFeatherType()->value,
            'createdAt'  => $vote->getCreatedAt()->format(DATE_ATOM),
            'voter'      => $vote->getVoter() ? [
                'id'     => $vote->getVoter()->getId(),
                'pseudo' => $vote->getVoter()->getPseudo(),
            ] : null,
            'poem'       => $vote->getPoem() ? [
                'id'    => $vote->getPoem()->getId(),
                'title' => $vote->getPoem()->getTitle(),
            ] : null,
        ];
    }

    /**
     * Transform a list of FeatherVote entities into an array payload.
     *
     * @param iterable<FeatherVote> $votes
     *
     * @return array<int,array<string,mixed>>
     */
    public static function fromCollection(iterable $votes): array
    {
        $items = [];

        foreach ($votes as $vote) {
            $items[] = self::fromEntity($vote);
        }

        return $items;
    }
}
