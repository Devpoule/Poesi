<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Author;

/**
 * @extends EntityRepositoryInterface<Author>
 */
interface AuthorRepositoryInterface extends EntityRepositoryInterface
{
    /**
     * Find an author by email.
     *
     * @param string $email
     *
     * @return Author|null
     */
    public function findOneByEmail(string $email): ?Author;

    /**
     * Find all authors.
     *
     * @return Author[]
     */
    public function findAll(): array;

    /**
     * Persist the given author.
     *
     * @param Author $author
     *
     * @return void
     */
    public function save(object $author): void;

    /**
     * Remove the given author.
     *
     * @param Author $author
     *
     * @return void
     */
    public function delete(object $author): void;
}
