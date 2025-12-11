<?php

namespace App\Domain\Service;

use App\Domain\Entity\Author;
use App\Domain\Enum\MoodColor;
use App\Domain\Exception\AuthorNotFoundException;
use App\Domain\Exception\TotemNotFoundException;
use App\Domain\Repository\AuthorRepositoryInterface;
use App\Domain\Repository\TotemRepositoryInterface;

/**
 * Domain service responsible for author lifecycle and profile changes.
 */
class AuthorService
{
    public function __construct(
        private AuthorRepositoryInterface $authorRepository,
        private TotemRepositoryInterface $totemRepository,
    ) {
    }

    /**
     * Create a new Author with the given pseudo, email and optional totem and mood color.
     *
     * @param string           $pseudo
     * @param string           $email
     * @param int|null         $totemId
     * @param MoodColor|null   $moodColor
     *
     * @return Author
     */
    public function createAuthor(
        string $pseudo,
        string $email,
        ?int $totemId = null,
        ?MoodColor $moodColor = null
    ): Author {
        $author = new Author();
        $author->setPseudo($pseudo);
        $author->setEmail($email);

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
     * Change the mood color of the given author.
     *
     * @param int       $authorId
     * @param MoodColor $moodColor
     *
     * @return Author
     */
    public function changeMoodColor(int $authorId, MoodColor $moodColor): Author
    {
        $author = $this->getAuthorOrFail($authorId);

        $author->setMoodColor($moodColor);
        $this->authorRepository->save($author);

        return $author;
    }

    /**
     * Attach a new totem to the given author.
     *
     * @param int $authorId
     * @param int $totemId
     *
     * @return Author
     */
    public function changeTotem(int $authorId, int $totemId): Author
    {
        $author = $this->getAuthorOrFail($authorId);

        $totem = $this->totemRepository->getById($totemId);
        if ($totem === null) {
            throw new TotemNotFoundException('Totem not found for id ' . $totemId . '.');
        }

        $author->setTotem($totem);
        $this->authorRepository->save($author);

        return $author;
    }

    /**
     * Update the main properties of an author.
     * Only non-null parameters will be updated.
     *
     * @param int             $authorId
     * @param string|null     $pseudo
     * @param MoodColor|null  $moodColor
     * @param int|null        $totemId
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
     * @param int $authorId
     *
     * @return void
     */
    public function deleteAuthor(int $authorId): void
    {
        $author = $this->getAuthorOrFail($authorId);

        $this->authorRepository->delete($author);
    }
}
