<?php

declare(strict_types=1);

namespace App\Tests\Domain\Entity;

use App\Domain\Entity\Symbol;
use PHPUnit\Framework\TestCase;

/**
 * 🎯 Validate symbol fields storage.
 */
class SymbolTest extends TestCase
{
    public function test_symbol_fields_are_persisted(): void
    {
        ## ───────| Arrange |─────── ##
        $symbol = (new Symbol())
            ->setKey('wings')
            ->setLabel('Ailes')
            ->setDescription('Elan, envol, liberté.')
            ->setPicture('/img/symbols/wings.svg')
            ;

        ## ───────| Act |─────── ##
        // getters only

        ## ───────| Assert |─────── ##
        $this->assertSame('wings', $symbol->getKey());
        $this->assertSame('Ailes', $symbol->getLabel());
        $this->assertSame('Elan, envol, liberté.', $symbol->getDescription());
        $this->assertSame('/img/symbols/wings.svg', $symbol->getPicture());
    }
}
