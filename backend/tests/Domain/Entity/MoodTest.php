<?php

declare(strict_types=1);

namespace App\Tests\Domain\Entity;

use App\Domain\Entity\Mood;
use PHPUnit\Framework\TestCase;

/**
 * 🎯 Ensure mood fields are stored and optional fields handled.
 */
class MoodTest extends TestCase
{
    public function test_mood_properties_are_persisted(): void
    {
        ## ───────| Arrange |─────── ##
        $mood = (new Mood())
            ->setKey('blue')
            ->setLabel('Bleu')
            ->setDescription('Calme, posé, maîtrisé.')
            ->setIcon('/icons/moods/blue.svg');

        ## ───────| Act |─────── ##
        // getters only

        ## ───────| Assert |─────── ##
        $this->assertSame('blue', $mood->getKey());
        $this->assertSame('Bleu', $mood->getLabel());
        $this->assertSame('Calme, posé, maîtrisé.', $mood->getDescription());
        $this->assertSame('/icons/moods/blue.svg', $mood->getIcon());
    }
}
