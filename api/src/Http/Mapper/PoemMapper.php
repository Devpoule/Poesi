<?php

namespace App\Http\Mapper;

use App\Domain\Entity\Poem;
use App\Domain\Entity\FeatherVote;

/**
 * Maps Poem domain entities to API-friendly arrays.
 *
 * This mapper is intentionally "read-only": it does not hydrate entities.
 * Its only job is to provide stable, predictable JSON structures for clients
 * (browser frontend, mobile frontend, etc.).
 */
final class PoemMapper
{
    public function __construct(
        private readonly AuthorMapper $authorMapper
    ) {
    }

    /**
     * Convert a Poem entity into an array representation for the API.
     *
     * @param Poem $poem
     *
     * @return array{
     *     id: int|null,
     *     title: string,
     *     content: string,
     *     status: string,
     *     moodColor: string,
     *     createdAt: string,
     *     publishedAt: string|null,
     *     author: array<string, mixed>|null,
     *     stats: array{
     *         votesTotal: int,
     *         votesBronze: int,
     *         votesSilver: int,
     *         votesGold: int
     *     }
     * }
     */
    public function toArray(Poem $poem): array
    {
        $stats = $this->computeVotesStats($poem);

        return [
            'id'          => $poem->getId(),
            'title'       => $poem->getTitle(),
            'content'     => $poem->getContent(),
            'status'      => $poem->getStatus()->value !== null ? $poem->getStatus()->value : null,
            'moodColor'   => $poem->getMoodColor() !== null ? $poem->getMoodColor()->value : null,
            'createdAt'   => $poem->getCreatedAt()->format(\DateTimeInterface::ATOM),
            'publishedAt' => $poem->getPublishedAt()?->format(\DateTimeInterface::ATOM),
            'author'      => $poem->getAuthor() !== null ? $this->authorMapper->toArray($poem->getAuthor()) : null,
            'stats'       => $stats,
        ];
    }

    /**
     * Convert a list of Poem entities into a list of array representations.
     *
     * @param Poem[] $poems
     *
     * @return array<int, array<string, mixed>>
     */
    public function toCollection(array $poems): array
    {
        $result = [];

        foreach ($poems as $poem) {
            if (!$poem instanceof Poem) {
                // Defensive programming: protects the API layer from invalid data.
                continue;
            }

            $result[] = $this->toArray($poem);
        }

        return $result;
    }

    /**
     * Compute aggregated feather vote statistics for a poem.
     *
     * This is a good place to keep "presentation-level" stats:
     * - It avoids leaking business rules into controllers.
     * - It keeps client payload stable (mobile/web can rely on it).
     *
     * If this ever becomes expensive, move it to a dedicated read model
     * or compute it in SQL and expose it via repository method.
     *
     * @param Poem $poem
     *
     * @return array{
     *     votesTotal: int,
     *     votesBronze: int,
     *     votesSilver: int,
     *     votesGold: int
     * }
     */
    private function computeVotesStats(Poem $poem): array
    {
        $bronze = 0;
        $silver = 0;
        $gold   = 0;

        foreach ($poem->getFeatherVotes() as $vote) {
            if (!$vote instanceof FeatherVote) {
                continue;
            }

            $type = $vote->getFeatherType()->value;

            if ($type === 'bronze') {
                $bronze++;
                continue;
            }

            if ($type === 'silver') {
                $silver++;
                continue;
            }

            if ($type === 'gold') {
                $gold++;
                continue;
            }
        }

        return [
            'votesTotal'  => $bronze + $silver + $gold,
            'votesBronze' => $bronze,
            'votesSilver' => $silver,
            'votesGold'   => $gold,
        ];
    }
}
