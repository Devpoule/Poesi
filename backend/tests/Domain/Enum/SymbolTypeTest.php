<?php

declare(strict_types=1);

namespace App\Tests\Domain\Enum;

use App\Domain\Enum\SymbolType;
use PHPUnit\Framework\TestCase;

/**
 * 🎯 Ensure SymbolType labels and descriptions map correctly.
 */
class SymbolTypeTest extends TestCase
{
    public function test_label_and_description_match_enum_values(): void
    {
        ## ───────| Arrange |─────── ##
        $symbol = SymbolType::HORIZON;

        ## ───────| Act |─────── ##
        $label = $symbol->label();
        $description = $symbol->description();

        ## ───────| Assert |─────── ##
        $this->assertSame('Horizon', $label);
        $this->assertStringContainsString('duration', strtolower($description));
    }
}
