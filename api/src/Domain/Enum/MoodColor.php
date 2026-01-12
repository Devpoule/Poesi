<?php

namespace App\Domain\Enum;

/**
 * Represents the dominant emotional color of an author or a poem.
 */
enum MoodColor: string
{
    case RED    = 'red';
    case ORANGE = 'orange';
    case YELLOW = 'yellow';
    case GREEN  = 'green';
    case BLUE   = 'blue';
    case INDIGO = 'indigo';
    case VIOLET = 'violet';
    case BLACK  = 'black';
    case WHITE  = 'white';
    case GREY   = 'grey';
}
