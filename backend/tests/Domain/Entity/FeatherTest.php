<?php

declare(strict_types=1);

namespace App\Tests\Domain\Entity;

use App\Domain\Entity\Feather;
use PHPUnit\Framework\TestCase;

/**
 * 🎯 Verify feather fields are stored and retrieved as expected.
 */
class FeatherTest extends TestCase
{
    public function test_feather_properties_are_persisted(): void
    {
        ## ───────| Arrange |─────── ##
        $feather = (new Feather())
            ->setKey('bronze')
            ->setLabel('Plume de Bronze')
            ->setDescription('Premiers échos.')
            ->setIcon('/icons/bronze.svg');

        ## ───────| Act |─────── ##
        // getters only

        ## ───────| Assert |─────── ##
        $this->assertSame('bronze', $feather->getKey());
        $this->assertSame('Plume de Bronze', $feather->getLabel());
        $this->assertSame('Premiers échos.', $feather->getDescription());
        $this->assertSame('/icons/bronze.svg', $feather->getIcon());
    }
}
