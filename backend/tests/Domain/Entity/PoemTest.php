<?php

declare(strict_types=1);

namespace App\Tests\Domain\Entity;

use App\Domain\Entity\Poem;
use App\Domain\Entity\User;
use App\Domain\Enum\MoodColor;
use App\Domain\Enum\PoemStatus;
use App\Domain\Enum\SymbolType;
use PHPUnit\Framework\TestCase;

/**
 * 🎯 Ensure newly created poems start with safe defaults.
 */
class PoemTest extends TestCase
{
    public function test_defaults_are_initialized(): void
    {
        ## ───────| Arrange |─────── ##
        $author = (new User())
            ->setEmail('author@test.local')
            ->setPassword('secret')
            ->setRoles(['ROLE_USER']);

        ## ───────| Act |─────── ##
        $poem = (new Poem())
            ->setAuthor($author)
            ->setTitle('Titre')
            ->setContent('Contenu');

        ## ───────| Assert |─────── ##
        $this->assertSame($author, $poem->getAuthor());
        $this->assertSame(PoemStatus::DRAFT, $poem->getStatus());
        $this->assertSame(MoodColor::BLUE, $poem->getMoodColor());
        $this->assertNotNull($poem->getCreatedAt());
        $this->assertNull($poem->getPublishedAt());
    }

    /**
     * 🎯 Ensure publishing sets the status and timestamp as expected.
     */
    public function test_can_publish_with_timestamp(): void
    {
        ## ───────| Arrange |─────── ##
        $author = (new User())
            ->setEmail('author@test.local')
            ->setPassword('secret')
            ->setRoles(['ROLE_USER']);
        $publishedAt = new \DateTimeImmutable('2024-01-01 10:00:00');

        ## ───────| Act |─────── ##
        $poem = (new Poem())
            ->setAuthor($author)
            ->setTitle('Titre')
            ->setContent('Contenu')
            ->setStatus(PoemStatus::PUBLISHED)
            ->setPublishedAt($publishedAt);

        ## ───────| Assert |─────── ##
        $this->assertSame(PoemStatus::PUBLISHED, $poem->getStatus());
        $this->assertSame($publishedAt, $poem->getPublishedAt());
    }

    /**
     * 🎯 Ensure mood color and symbol are set/retrieved consistently.
     */
    public function test_can_set_mood_and_symbol(): void
    {
        ## ───────| Arrange |─────── ##
        $author = (new User())
            ->setEmail('author@test.local')
            ->setPassword('secret')
            ->setRoles(['ROLE_USER']);

        ## ───────| Act |─────── ##
        $poem = (new Poem())
            ->setAuthor($author)
            ->setTitle('Titre')
            ->setContent('Contenu')
            ->setMoodColor(MoodColor::RED)
            ->setSymbolType(SymbolType::WINGS);

        ## ───────| Assert |─────── ##
        $this->assertSame(MoodColor::RED, $poem->getMoodColor());
        $this->assertSame(SymbolType::WINGS, $poem->getSymbolType());
    }

    /**
     * 🎯 Ensure symbol can be cleared when needed.
     */
    public function test_symbol_can_be_cleared(): void
    {
        ## ───────| Arrange |─────── ##
        $author = (new User())
            ->setEmail('author@test.local')
            ->setPassword('secret')
            ->setRoles(['ROLE_USER']);

        $poem = (new Poem())
            ->setAuthor($author)
            ->setTitle('Titre')
            ->setContent('Contenu')
            ->setSymbolType(SymbolType::HORIZON);

        ## ───────| Act |─────── ##
        $poem->setSymbolType(null);

        ## ───────| Assert |─────── ##
        $this->assertNull($poem->getSymbolType());
    }
}
