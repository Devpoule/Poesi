<?php

namespace App\Domain\Enum;

/**
 * Represents the type of feather vote given to a poem.
 */
enum FeatherType: string
{
    case BRONZE = 'bronze';
    case SILVER = 'silver';
    case GOLD   = 'gold';
}
