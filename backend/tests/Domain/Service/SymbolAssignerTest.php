<?php

declare(strict_types=1);

namespace App\Tests\Domain\Service;

use App\Domain\Entity\FeatherVote;
use App\Domain\Entity\Poem;
use App\Domain\Entity\User;
use App\Domain\Enum\FeatherType;
use App\Domain\Enum\MoodColor;
use App\Domain\Enum\SymbolType;
use App\Domain\Service\SymbolAssigner;
use PHPUnit\Framework\TestCase;

class SymbolAssignerTest extends TestCase
{
    /**
     * 🎯 low score + dynamic mood yields WINGS.
     */
    public function test_low_score_dynamic_mood_returns_wings(): void
    {
        ## --------| Arrange |-------- ##
        $poem = (new Poem())
            ->setMoodColor(MoodColor::RED)
            ->setTitle('Titre')
            ->setContent('Texte');
        $assigner = new SymbolAssigner();

        ## --------| Act |-------- ##
        $symbol = $assigner->compute($poem);

        ## --------| Assert |-------- ##
        $this->assertSame(SymbolType::WINGS, $symbol);
    }

    /**
     * 🎯 mid score + contemplative mood yields HORIZON.
     */
    public function test_mid_score_contemplative_mood_returns_horizon(): void
    {
        ## --------| Arrange |-------- ##
        $poem = (new Poem())
            ->setMoodColor(MoodColor::BLUE)
            ->setTitle('Titre')
            ->setContent('Texte');

        // score: 21 (3 gold)
        $poem->addFeatherVote((new FeatherVote())->setFeatherType(FeatherType::GOLD));
        $poem->addFeatherVote((new FeatherVote())->setFeatherType(FeatherType::GOLD));
        $poem->addFeatherVote((new FeatherVote())->setFeatherType(FeatherType::GOLD));

        $assigner = new SymbolAssigner();

        ## --------| Act |-------- ##
        $symbol = $assigner->compute($poem);

        ## --------| Assert |-------- ##
        $this->assertSame(SymbolType::HORIZON, $symbol);
    }

    /**
     * 🎯 high score + deep mood yields VORTEX.
     */
    public function test_high_score_deep_mood_returns_vortex(): void
    {
        ## --------| Arrange |-------- ##
        $poem = (new Poem())
            ->setMoodColor(MoodColor::VIOLET)
            ->setTitle('Titre')
            ->setContent('Texte');

        // score: 63 (9 gold)
        for ($i = 0; $i < 9; ++$i) {
            $poem->addFeatherVote((new FeatherVote())->setFeatherType(FeatherType::GOLD));
        }

        $assigner = new SymbolAssigner();

        ## --------| Act |-------- ##
        $symbol = $assigner->compute($poem);

        ## --------| Assert |-------- ##
        $this->assertSame(SymbolType::VORTEX, $symbol);
    }
}
