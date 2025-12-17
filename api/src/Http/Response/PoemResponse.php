<?php

namespace App\Http\Response;

use App\Domain\Entity\Poem;
use App\Domain\Entity\FeatherVote;

final class PoemResponse
{
    private const DEFAULT_TOTEM_ID = 1;

    /**
     * @return array{
     *   id:int|null,
     *   title:string,
     *   content:string,
     *   status:string,
     *   moodColor:string|null,
     *   createdAt:string,
     *   publishedAt:string|null,
     *   author:array{id:int|null,pseudo:string|null,totemId:int|null}|null,
     *   publish:array{canPublish:bool,reason:string|null},
     *   stats:array{votesTotal:int,votesBronze:int,votesSilver:int,votesGold:int}
     * }
     */
    public function item(Poem $poem): array
    {
        $author = $poem->getAuthor();
        $authorTotemId = $author?->getTotem()?->getId();

        $canPublish = $authorTotemId !== null && $authorTotemId !== self::DEFAULT_TOTEM_ID;

        return [
            'id'          => $poem->getId(),
            'title'       => $poem->getTitle(),
            'content'     => $poem->getContent(),
            'status'      => $poem->getStatus()->value,
            'moodColor'   => $poem->getMoodColor()?->value,
            'createdAt'   => $poem->getCreatedAt()->format(\DateTimeInterface::ATOM),
            'publishedAt' => $poem->getPublishedAt()?->format(\DateTimeInterface::ATOM),

            'author' => $author !== null ? [
                'id'      => $author->getId(),
                'pseudo'  => $author->getPseudo(),
                'totemId' => $authorTotemId,
            ] : null,

            'publish' => [
                'canPublish' => $canPublish,
                'reason'     => $canPublish ? null : 'Choose a totem before publishing.',
            ],

            'stats' => $this->computeVotesStats($poem),
        ];
    }

    /**
     * @param iterable<Poem> $poems
     *
     * @return array<int, array<string, mixed>>
     */
    public function collection(iterable $poems): array
    {
        $result = [];

        foreach ($poems as $poem) {
            $result[] = $this->item($poem);
        }

        return $result;
    }

    /**
     * @return array{votesTotal:int,votesBronze:int,votesSilver:int,votesGold:int}
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

            $type = $vote->getFeatherType()?->value;

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
