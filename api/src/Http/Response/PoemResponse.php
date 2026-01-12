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
     * @param Poem $poem
     * 
     * @return array{user: array{id: int|null, pseudo: string, totemId: int|null|null, content: string, createdAt: string, featherVotes: array{votesBronze: int, votesGold: int, votesSilver: int, votesTotal: int}, feathers: array, id: int|null, mood: array{description: string, icon: string, label: string}|null, moodColor: string|null, publish: array{canPublish: bool, reason: string|null}, publishedAt: string|null, stats: array{votesBronze: int, votesGold: int, votesSilver: int, votesTotal: int}, status: string, symbol: array{description: string, label: string, picture: string}|null, symbolType: string|null, title: string}}
     */
    public function item(Poem $poem): array
    {
        $user = $poem->getAuthor();
        $userTotemId = $user?->getTotem()?->getId();

        $canPublish = $userTotemId !== null && $userTotemId !== self::DEFAULT_TOTEM_ID;

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

            'user' => $user !== null ? [
                'id'      => $user->getId(),
                'pseudo'  => $user->getPseudo(),
                'totemId' => $userTotemId,
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
