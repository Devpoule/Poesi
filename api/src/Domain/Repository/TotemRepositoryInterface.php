<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Totem;

/**
 * @extends EntityRepositoryInterface<Totem>
 */
interface TotemRepositoryInterface extends EntityRepositoryInterface
{
    /**
     * Return all totems ordered for display.
     *
     * @return Totem[]
     */
    public function findAllOrdered(): array;

    /**
     * Find all totems.
     *
     * @return Totem[]
     */
    public function findAll(): array;

    /**
     * Persist the given totem.
     *
     * @param Totem $totem
     *
     * @return void
     */
    public function save(object $totem): void;

    /**
     * Remove the given totem.
     *
     * @param Totem $totem
     *
     * @return void
     */
    public function delete(object $totem): void;
}
