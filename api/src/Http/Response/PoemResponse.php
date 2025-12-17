<?php

namespace App\Http\Response;

use App\Domain\Entity\Poem;
use App\Domain\Entity\FeatherVote;
use App\Domain\Enum\FeatherType;
use App\Domain\Enum\SymbolType;
use App\Domain\Lore\LoreCatalog;

final class PoemResponse
{
    private const DEFAULT_TOTEM_ID = 1;

    public function __construct(
        private readonly LoreCatalog $loreCatalog,
    ) {
    }

    /**
     * @return array{
     *   id:int|null,
     *   title:string,
     *   content:string,
     *   status:string,
     *   moodColor:string|null,
     *   mood:array{label:string,description:string,icon:string}|null,
     *   symbolType:string|null,
     *   symbol:array{label:string,description:string,picture:string}|null,
     *   createdAt:string,
     *   publishedAt:string|null,
     *   author:array{id:int|null,pseudo:string|null,totemId:int|null}|null,
     *   publish:array{canPublish:bool,reason:string|null},
     *   featherVotes:array{votesTotal:int,votesBronze:int,votesSilver:int,votesGold:int},
     *   stats:array{votesTotal:int,votesBronze:int,votesSilver:int,votesGold:int},
     *   feathers:array{
     *     bronze:array{label:string,description:string,icon:string},
     *     silver:array{label:string,description:string,icon:string},
     *     gold:array{label:string,description:string,icon:string}
     *   }
     * }
     */
    public function item(Poem $poem): array
    {
        $author = $poem->getAuthor();
        $authorTotemId = $author?->getTotem()?->getId();

        $canPublish = $authorTotemId !== null && $authorTotemId !== self::DEFAULT_TOTEM_ID;

        $moodColor = $poem->getMoodColor();
        $symbolType = $poem->getSymbolType();

        $featherVotes = $this->computeFeatherVotesStats($poem);

        return [
            'id'          => $poem->getId(),
            'title'       => $poem->getTitle(),
            'content'     => $poem->getContent(),
            'status'      => $poem->getStatus()->value,

            'moodColor'   => $moodColor?->value,
            'mood'        => $moodColor !== null ? $this->loreCatalog->getMood($moodColor) : null,

            'symbolType'  => $symbolType?->value,
            'symbol'      => $symbolType !== null ? $this->loreCatalog->getSymbol($symbolType) : null,

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

            'featherVotes' => $featherVotes,
            'stats'        => $featherVotes, // legacy alias (remove later)

            'feathers' => [
                'bronze' => $this->loreCatalog->getFeather(FeatherType::BRONZE),
                'silver' => $this->loreCatalog->getFeather(FeatherType::SILVER),
                'gold'   => $this->loreCatalog->getFeather(FeatherType::GOLD),
            ],
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
    private function computeFeatherVotesStats(Poem $poem): array
    {
        $counts = [
            FeatherType::BRONZE->value => 0,
            FeatherType::SILVER->value => 0,
            FeatherType::GOLD->value   => 0,
        ];

        foreach ($poem->getFeatherVotes() as $vote) {
            if (!$vote instanceof FeatherVote) {
                continue;
            }

            $type = $vote->getFeatherType();
            if ($type === null) {
                continue;
            }

            // If any unexpected value appears, fail safely by ignoring it.
            if (!array_key_exists($type->value, $counts)) {
                continue;
            }

            $counts[$type->value]++;
        }

        return [
            'votesTotal'  => $counts['bronze'] + $counts['silver'] + $counts['gold'],
            'votesBronze' => $counts['bronze'],
            'votesSilver' => $counts['silver'],
            'votesGold'   => $counts['gold'],
        ];
    }
}
