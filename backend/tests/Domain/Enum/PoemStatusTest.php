<?php

declare(strict_types=1);

namespace App\Tests\Domain\Enum;

use App\Domain\Enum\PoemStatus;
use PHPUnit\Framework\TestCase;

/**
 * 🎯 Ensure poem statuses expose the expected values.
 */
class PoemStatusTest extends TestCase
{
    public function test_poem_status_values(): void
    {
        ## ───────| Arrange |─────── ##
        $values = PoemStatus::cases();

        ## ───────| Act |─────── ##
        $names = array_map(static fn (PoemStatus $status) => $status->value, $values);

        ## ───────| Assert |─────── ##
        $this->assertContains('draft', $names);
        $this->assertContains('published', $names);
        $this->assertCount(2, $values);
    }
}
