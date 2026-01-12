<?php

namespace App\Domain\Service;

use App\Domain\Entity\Poem;
use App\Domain\Enum\MoodColor;
use App\Domain\Enum\SymbolType;

/**
 * Computes the evolving SymbolType of a poem from its mood and feather votes.
 *
 * This is pure domain logic: no I/O, no persistence.
 */
final class SymbolAssigner
{
    public function compute(Poem $poem): ?SymbolType
    {
        $mood = $poem->getMoodColor();
        if (!$mood instanceof MoodColor) {
            return null;
        }

        $score = $this->computeScore($poem);

        if ($score < 20) {
            return $this->pickForLow($mood);
        }

        if ($score < 60) {
            return $this->pickForMid($mood);
        }

        return $this->pickForHigh($mood);
    }

    private function computeScore(Poem $poem): int
    {
        $bronze = 0; $silver = 0; $gold = 0;

        foreach ($poem->getFeatherVotes() as $vote) {
            $type = $vote->getFeatherType();
            if ($type === null) {
                continue;
            }

            $bronze += ($type->value === 'bronze') ? 1 : 0;
            $silver += ($type->value === 'silver') ? 1 : 0;
            $gold   += ($type->value === 'gold')   ? 1 : 0;
        }

        return ($bronze * 1) + ($silver * 3) + ($gold * 7);
    }

    private function pickForLow(MoodColor $mood): SymbolType
    {
        return match (true) {
            $this->isDynamic($mood)      => SymbolType::WINGS,
            $this->isDeep($mood)         => SymbolType::VORTEX,
            $this->isContemplative($mood)=> SymbolType::HORIZON,
            default                      => SymbolType::HALO,
        };
    }

    private function pickForMid(MoodColor $mood): SymbolType
    {
        return match (true) {
            $this->isDynamic($mood)      => SymbolType::METEOR_SHARD,
            $this->isDeep($mood)         => SymbolType::VORTEX,
            $this->isContemplative($mood)=> SymbolType::HORIZON,
            default                      => SymbolType::WINGS,
        };
    }

    private function pickForHigh(MoodColor $mood): SymbolType
    {
        // Same mapping as mid; "prestige" can be expressed via stronger animation based on score.
        return $this->pickForMid($mood);
    }

    private function isDynamic(MoodColor $mood): bool
    {
        return in_array($mood, [MoodColor::RED, MoodColor::ORANGE], true);
    }

    private function isDeep(MoodColor $mood): bool
    {
        return in_array($mood, [MoodColor::VIOLET, MoodColor::BLACK], true);
    }

    private function isContemplative(MoodColor $mood): bool
    {
        return in_array($mood, [MoodColor::BLUE, MoodColor::INDIGO, MoodColor::GREY], true);
    }
}
