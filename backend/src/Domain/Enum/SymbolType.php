<?php

namespace App\Domain\Enum;

/**
 * Represents the evolving visual emblem of a poem.
 */
enum SymbolType: string
{
    case WINGS        = 'wings';
    case METEOR_SHARD = 'meteor_shard';
    case VORTEX       = 'vortex';
    case HORIZON      = 'horizon';
    case HALO         = 'halo';

    /**
     * Human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::WINGS        => 'Ailes',
            self::METEOR_SHARD => 'Fragment de Météore',
            self::VORTEX       => 'Tourbillon',
            self::HORIZON      => 'Horizon',
            self::HALO         => 'Halo',
        };
    }

    /**
     * Symbolic meaning.
     */
    public function description(): string
    {
        return match ($this) {
            self::WINGS =>
                'The poem rises naturally. It finds its breath and moves forward with lightness.',

            self::METEOR_SHARD =>
                'A brief but sharp impact. Few words are enough to leave a lasting mark.',

            self::VORTEX =>
                'An inner intensity that pulls and holds. The poem draws the reader in.',

            self::HORIZON =>
                'A poem built for duration. It opens up, accompanies, and remains present.',

            self::HALO =>
                'Clarity first. The poem illuminates without forcing, with simplicity.',
        };
    }
}
