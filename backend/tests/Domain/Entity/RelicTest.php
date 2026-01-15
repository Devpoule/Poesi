<?php

declare(strict_types=1);

namespace App\Tests\Domain\Entity;

use App\Domain\Entity\Relic;
use PHPUnit\Framework\TestCase;

/**
 * 🎯 Validate relic catalog fields.
 */
class RelicTest extends TestCase
{
    public function test_relic_fields_are_persisted(): void
    {
        ## ───────| Arrange |─────── ##
        $relic = (new Relic())
            ->setKey('phoenix')
            ->setLabel('Plume de Phénix')
            ->setDescription('Renaissance symbolique.')
            ->setPicture('/img/relics/phoenix.svg')
            ->setRarity('epic');

        ## ───────| Act |─────── ##
        // getters only

        ## ───────| Assert |─────── ##
        $this->assertSame('phoenix', $relic->getKey());
        $this->assertSame('Plume de Phénix', $relic->getLabel());
        $this->assertSame('Renaissance symbolique.', $relic->getDescription());
        $this->assertSame('/img/relics/phoenix.svg', $relic->getPicture());
        $this->assertSame('epic', $relic->getRarity());
    }
}
