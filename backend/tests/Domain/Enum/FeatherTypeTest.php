<?php

declare(strict_types=1);

namespace App\Tests\Domain\Enum;

use App\Domain\Enum\FeatherType;
use PHPUnit\Framework\TestCase;

/**
 * 🎯 Ensure feather types include bronze/silver/gold.
 */
class FeatherTypeTest extends TestCase
{
    public function test_expected_feather_types(): void
    {
        ## ───────| Arrange |─────── ##
        $values = FeatherType::cases();

        ## ───────| Act |─────── ##
        $names = array_map(static fn (FeatherType $type) => $type->value, $values);

        ## ───────| Assert |─────── ##
        $this->assertContains('bronze', $names);
        $this->assertContains('silver', $names);
        $this->assertContains('gold', $names);
        $this->assertCount(3, $values);
    }
}
