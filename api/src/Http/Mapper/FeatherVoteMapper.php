<?php

namespace App\Http\Mapper;

use App\Domain\Entity\FeatherVote;

/**
 * Maps FeatherVote domain entities to API-friendly arrays.
 *
 * This mapper is intentionally lightweight:
 * - It avoids embedding full Poem/Author payloads to prevent recursion and large responses.
 * - Mobile/web clients can fetch details through dedicated endpoints if needed.
 */
final class FeatherVoteMapper
{
    /**
     * @param FeatherVote $vote
     *
     * @return array{
     *   id: int|null,
     *   featherType: string,
     *   createdAt: string,
     *   updatedAt: string,
     *   voter: array{id: int|null, pseudo: string|null},
     *   poem: array{id: int|null, title: string|null}
     * }
     */
    public function toArray(FeatherVote $vote): array
    {
        $voter = $vote->getVoter();
        $poem = $vote->getPoem();

        return [
            'id' => $vote->getId(),
            'featherType' => $vote->getFeatherType() !== null ? $vote->getFeatherType()->value : null,
            'createdAt' => $vote->getCreatedAt()->format(\DateTimeInterface::ATOM),
            'updatedAt' => $vote->getUpdatedAt()->format(\DateTimeInterface::ATOM),
            'voter' => [
                'id' => $voter?->getId(),
                'pseudo' => $voter?->getPseudo(),
            ],
            'poem' => [
                'id' => $poem?->getId(),
                'title' => $poem?->getTitle(),
            ],
        ];
    }

    /**
     * @param FeatherVote[] $votes
     *
     * @return array<int, array<string, mixed>>
     */
    public function toCollection(array $votes): array
    {
        $result = [];

        foreach ($votes as $vote) {
            if (!$vote instanceof FeatherVote) {
                continue;
            }

            $result[] = $this->toArray($vote);
        }

        return $result;
    }
}
