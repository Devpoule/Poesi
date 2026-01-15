<?php

declare(strict_types=1);

namespace App\Tests\Domain\Enum;

use App\Domain\Enum\MoodColor;
use PHPUnit\Framework\TestCase;

/**
 * 🎯 Ensure MoodColor values are stable.
 */
class MoodColorTest extends TestCase
{
    public function test_all_expected_mood_colors_exist(): void
    {
        ## ───────| Arrange |─────── ##
        $values = MoodColor::cases();

        ## ───────| Act |─────── ##
        $keys = array_map(static fn (MoodColor $m) => $m->value, $values);

        ## ───────| Assert |─────── ##
        $this->assertContains('red', $keys);
        $this->assertContains('blue', $keys);
        $this->assertContains('white', $keys);
        $this->assertCount(10, $values);
    }
}
