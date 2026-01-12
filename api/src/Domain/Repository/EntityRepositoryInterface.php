<?php

namespace App\Domain\Repository;

/**
 * Base contract for simple entity repositories.
 *
 * @template T of object
 */
interface EntityRepositoryInterface
{
    /**
     * Find an entity by its identifier.
     *
     * @param int $id
     *
     * @return T|null
     */
    public function getById(int $id): ?object;

    /**
     * Return all entities.
     *
     * @return T[]
     */
    public function findAll(): array;

    /**
     * Persist the given entity.
     *
     * @param T $entity
     *
     * @return void
     */
    public function save(object $entity): void;

    /**
     * Remove the given entity.
     *
     * @param T $entity
     *
     * @return void
     */
    public function delete(object $entity): void;
}
