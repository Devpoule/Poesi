<?php

declare(strict_types=1);

namespace App\Tests\Domain\Entity;

use App\Domain\Entity\FeatherVote;
use App\Domain\Entity\Poem;
use App\Domain\Entity\User;
use App\Domain\Enum\FeatherType;
use PHPUnit\Framework\TestCase;

/**
 * 🎯 Ensure feather votes track voter/poem and update timestamps on change.
 */
class FeatherVoteTest extends TestCase
{
    public function test_vote_defaults_and_feather_update_touch(): void
    {
        ## ───────| Arrange |─────── ##
        $voter = (new User())
            ->setEmail('voter@test.local')
            ->setPassword('secret')
            ->setRoles(['ROLE_USER']);

        $poem = (new Poem())
            ->setAuthor(
                (new User())
                    ->setEmail('author@test.local')
                    ->setPassword('secret')
                    ->setRoles(['ROLE_USER'])
            )
            ->setTitle('Titre')
            ->setContent('Contenu');

        $vote = new FeatherVote();
        $before = $vote->getUpdatedAt();

        ## ───────| Act |─────── ##
        $vote
            ->setVoter($voter)
            ->setPoem($poem)
            ->setFeatherType(FeatherType::GOLD);
        $after = $vote->getUpdatedAt();

        ## ───────| Assert |─────── ##
        $this->assertSame($voter, $vote->getVoter());
        $this->assertSame($poem, $vote->getPoem());
        $this->assertSame(FeatherType::GOLD, $vote->getFeatherType());
        $this->assertGreaterThan($before, $after);
    }

    public function test_touch_updates_timestamp(): void
    {
        ## ───────| Arrange |─────── ##
        $vote = new FeatherVote();
        $before = $vote->getUpdatedAt();

        ## ───────| Act |─────── ##
        $vote->touch();
        $after = $vote->getUpdatedAt();

        ## ───────| Assert |─────── ##
        $this->assertGreaterThan($before, $after);
    }
}
