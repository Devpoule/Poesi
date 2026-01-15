<?php

declare(strict_types=1);

namespace App\Tests\Domain\Entity;

use App\Domain\Entity\Poem;
use App\Domain\Entity\Totem;
use App\Domain\Entity\User;
use App\Domain\Enum\MoodColor;
use PHPUnit\Framework\TestCase;

/**
 * 🎯 Validate core user properties and relations.
 */
class UserTest extends TestCase
{
    public function test_user_core_fields_and_roles(): void
    {
        ## ───────| Arrange |─────── ##
        $user = (new User())
            ->setEmail('user@test.local')
            ->setPassword('secret')
            ->setRoles(['ROLE_USER'])
            ->setPseudo('Auteur')
            ->setMoodColor(MoodColor::INDIGO);

        ## ───────| Act |─────── ##
        // getters only

        ## ───────| Assert |─────── ##
        $this->assertSame('user@test.local', $user->getEmail());
        $this->assertSame('Auteur', $user->getPseudo());
        $this->assertSame(MoodColor::INDIGO, $user->getMoodColor());
        $this->assertSame(['ROLE_USER'], $user->getRoles());
    }

    public function test_poem_relation_is_synced(): void
    {
        ## ───────| Arrange |─────── ##
        $user = (new User())
            ->setEmail('author@test.local')
            ->setPassword('secret')
            ->setRoles(['ROLE_USER']);

        $poem = (new Poem())
            ->setTitle('Titre')
            ->setContent('Contenu');

        ## ───────| Act |─────── ##
        $user->addPoem($poem);

        ## ───────| Assert |─────── ##
        $this->assertTrue($user->getPoems()->contains($poem));
        $this->assertSame($user, $poem->getAuthor());
    }

    public function test_poem_relation_is_removed_symmetrically(): void
    {
        ## ───────| Arrange |─────── ##
        $user = (new User())
            ->setEmail('author2@test.local')
            ->setPassword('secret')
            ->setRoles(['ROLE_USER']);

        $poem = (new Poem())
            ->setTitle('Titre')
            ->setContent('Contenu');
        $user->addPoem($poem);

        ## ───────| Act |─────── ##
        $user->removePoem($poem);

        ## ───────| Assert |─────── ##
        $this->assertFalse($user->getPoems()->contains($poem));
        $this->assertNull($poem->getAuthor());
    }

    public function test_totem_relation_is_synced(): void
    {
        ## ───────| Arrange |─────── ##
        $user = (new User())
            ->setEmail('totem@test.local')
            ->setPassword('secret')
            ->setRoles(['ROLE_USER']);

        $totem = (new Totem())
            ->setKey('swan')
            ->setName('Cygne');

        ## ───────| Act |─────── ##
        $user->setTotem($totem);

        ## ───────| Assert |─────── ##
        $this->assertSame($totem, $user->getTotem());
        $this->assertTrue($totem->getUsers()->contains($user));
    }

    public function test_failed_login_counters_and_lock(): void
    {
        ## ───────| Arrange |─────── ##
        $user = (new User())
            ->setEmail('lock@test.local')
            ->setPassword('secret')
            ->setRoles(['ROLE_USER']);

        ## ───────| Act |─────── ##
        $user->incrementFailedLoginAttempts();
        $user->incrementFailedLoginAttempts();
        $user->lock();
        $lockedBeforeReset = $user->isLocked();
        $attemptsBeforeReset = $user->getFailedLoginAttempts();
        $user->resetFailedLoginAttempts();

        ## ───────| Assert |─────── ##
        $this->assertSame(2, $attemptsBeforeReset);
        $this->assertTrue($lockedBeforeReset);
        $this->assertSame(0, $user->getFailedLoginAttempts());
        $this->assertFalse($user->isLocked());
    }
}
