<?php

namespace App\Domain\Service;

use App\Domain\Entity\Author;
use App\Domain\Enum\MoodColor;
use App\Domain\Exception\NotFound\AuthorNotFoundException;
use App\Domain\Exception\CannotDelete\CannotDeleteAuthorException;
use App\Domain\Exception\NotFound\TotemNotFoundException;
use App\Domain\Repository\AuthorRepositoryInterface;
use App\Domain\Repository\TotemRepositoryInterface;

/**
 * Domain service responsible for author lifecycle and profile changes.
 */
final class AuthorService
{
    public function __construct(
        private AuthorRepositoryInterface $authorRepository,
        private TotemRepositoryInterface $totemRepository,
    ) {
    }

    /**
     * Create a new Author with the given pseudo, email and totem.
     *
     * Totem is required because Author::totem is non-nullable in Doctrine mapping.
     *
     * @param string         $pseudo
     * @param string         $email
     * @param int            $totemId
     * @param MoodColor|null $moodColor
     *
     * @return Author
     */
    public function createAuthor(
        string $pseudo,
        string $email,
        int $totemId,
        ?MoodColor $moodColor = null
    ): Author {
        $totem = $this->totemRepository->getById($totemId);

        if ($totem === null) {
            throw new TotemNotFoundException('Totem not found for id ' . $totemId . '.');
        }

        $author = new Author();
        $author->setPseudo($pseudo);
        $author->setEmail($email);
        $author->setTotem($totem);

        if ($moodColor !== null) {
            $author->setMoodColor($moodColor);
        }

        $this->authorRepository->save($author);

        return $author;
    }

    /**
     * Retrieve an Author by id or throw an exception when it does not exist.
     *
     * @param int $authorId
     *
     * @return Author
     */
    public function getAuthorOrFail(int $authorId): Author
    {
        $author = $this->authorRepository->getById($authorId);

        if ($author === null) {
            throw new AuthorNotFoundException('Author not found for id ' . $authorId . '.');
        }

        return $author;
    }

    /**
     * Return all authors.
     *
     * @return Author[]
     */
    public function listAll(): array
    {
        return $this->authorRepository->findAll();
    }

    /**
     * Update the main properties of an author.
     * Only non-null parameters will be updated.
     *
     * @param int            $authorId
     * @param string|null    $pseudo
     * @param MoodColor|null $moodColor
     * @param int|null       $totemId
     *
     * @return Author
     */
    public function updateAuthor(
        int $authorId,
        ?string $pseudo = null,
        ?MoodColor $moodColor = null,
        ?int $totemId = null
    ): Author {
        $author = $this->getAuthorOrFail($authorId);

        if ($pseudo !== null) {
            $author->setPseudo($pseudo);
        }

        if ($moodColor !== null) {
            $author->setMoodColor($moodColor);
        }

        if ($totemId !== null) {
            $totem = $this->totemRepository->getById($totemId);

            if ($totem === null) {
                throw new TotemNotFoundException('Totem not found for id ' . $totemId . '.');
            }

            $author->setTotem($totem);
        }

        $this->authorRepository->save($author);

        return $author;
    }

    /**
     * Delete an author by id.
     *
     * We explicitly prevent deletion when relations exist to avoid
     * database foreign key violations and keep a clean API contract.
     *
     * @param int $authorId
     *
     * @return void
     */
    public function deleteAuthor(int $authorId): void
    {
        $author = $this->getAuthorOrFail($authorId);

        // These checks keep behavior stable across DB engines.
        if ($author->getPoems()->count() > 0) {
            throw new CannotDeleteAuthorException('Cannot delete author: poems exist.');
        }

        if ($author->getFeatherVotes()->count() > 0) {
            throw new CannotDeleteAuthorException('Cannot delete author: votes exist.');
        }

        if ($author->getAuthorRewards()->count() > 0) {
            throw new CannotDeleteAuthorException('Cannot delete author: rewards exist.');
        }

        $this->authorRepository->delete($author);
    }
}
