<?php

declare(strict_types=1);

namespace App\Tests\Domain\Entity;

use App\Domain\Entity\Totem;
use App\Domain\Entity\User;
use PHPUnit\Framework\TestCase;

/**
 * 🎯 Ensure totem data and user relations stay consistent.
 */
class TotemTest extends TestCase
{
    public function test_totem_fields_and_user_relation(): void
    {
        ## ───────| Arrange |─────── ##
        $totem = (new Totem())
            ->setKey('owl')
            ->setName('Chouette')
            ->setDescription('Nocturne et lucide.')
            ->setPicture('/img/totems/owl.svg');

        $user = (new User())
            ->setEmail('owl@test.local')
            ->setPassword('secret')
            ->setRoles(['ROLE_USER']);

        ## ───────| Act |─────── ##
        $totem->addUser($user);

        ## ───────| Assert |─────── ##
        $this->assertSame('owl', $totem->getKey());
        $this->assertSame('Chouette', $totem->getName());
        $this->assertSame('Nocturne et lucide.', $totem->getDescription());
        $this->assertSame('/img/totems/owl.svg', $totem->getPicture());
        $this->assertTrue($totem->getUsers()->contains($user));
        $this->assertSame($totem, $user->getTotem());
    }

    public function test_remove_user_updates_inverse_side(): void
    {
        ## ───────| Arrange |─────── ##
        $totem = (new Totem())
            ->setKey('falcon')
            ->setName('Faucon');
        $user = (new User())
            ->setEmail('falcon@test.local')
            ->setPassword('secret')
            ->setRoles(['ROLE_USER']);
        $totem->addUser($user);

        ## ───────| Act |─────── ##
        $totem->removeUser($user);

        ## ───────| Assert |─────── ##
        $this->assertFalse($totem->getUsers()->contains($user));
        $this->assertNull($user->getTotem());
    }
}
