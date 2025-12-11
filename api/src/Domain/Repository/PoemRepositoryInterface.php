<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Author;
use App\Domain\Entity\Poem;

/**
 * @extends EntityRepositoryInterface<Poem>
 */
interface PoemRepositoryInterface extends EntityRepositoryInterface
{
    /**
     * Find all poems for a given author.
     *
     * @param Author $author
     *
     * @return Poem[]
     */
    public function findByAuthor(Author $author): array;

    /**
     * Find all published poems for a given author.
     *
     * @param Author $author
     *
     * @return Poem[]
     */
    public function findPublishedByAuthor(Author $author): array;

    /**
     * Find all poems.
     *
     * @return Poem[]
     */
    public function findAll(): array;

    /**
     * Persist the given poem.
     *
     * @param Poem $poem
     *
     * @return void
     */
    public function save(object $poem): void;

    /**
     * Remove the given poem.
     *
     * @param Poem $poem
     *
     * @return void
     */
    public function delete(object $poem): void;
}
