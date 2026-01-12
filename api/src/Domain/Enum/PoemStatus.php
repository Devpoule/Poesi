<?php

namespace App\Domain\Enum;

/**
 * Represents the publication status of a poem.
 */
enum PoemStatus: string
{
    case DRAFT     = 'draft';
    case PUBLISHED = 'published';
}
