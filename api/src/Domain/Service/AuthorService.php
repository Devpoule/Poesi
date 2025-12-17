<?php

namespace App\Domain\Service;

use App\Domain\Entity\Author;
use App\Domain\Enum\MoodColor;
use App\Domain\Exception\CannotDelete\CannotDeleteAuthorException;
use App\Domain\Exception\NotFound\AuthorNotFoundException;
use App\Domain\Exception\NotFound\TotemNotFoundException;
use App\Domain\Repository\AuthorRepositoryInterface;
use App\Domain\Repository\TotemRepositoryInterface;

/**
 * Domain service responsible for author lifecycle and profile changes.
 */
final class AuthorService
{
    private const DEFAULT_TOTEM_ID = 1;

    public function __construct(
        private AuthorRepositoryInterface $authorRepository,
        private TotemRepositoryInterface $totemRepository,
    ) {
    }

    /**
     * Create a new Author with the given pseudo and email.
     *
     * Totem is assigned by default to DEFAULT_TOTEM_ID ("Oeuf").
     * Optionally, you may assign a random totem excluding the default one.
     *
     * @param string $pseudo
     * @param string $email
     * @param mixed $totemId
     * @param mixed $moodColor
     * @param bool $randomTotem
     * @throws TotemNotFoundException
     * @return Author
     */
    public function createAuthor(
        string $pseudo,
        string $email,
        ?int $totemId = null,
        ?MoodColor $moodColor = null,
        bool $randomTotem = false
    ): Author {
        $totem = null;

        if ($totemId !== null) {
            $totem = $this->totemRepository->getById($totemId);
            if ($totem === null) {
                throw new TotemNotFoundException('Totem not found for id ' . $totemId . '.');
            }
        }

        if ($totem === null && $randomTotem === true) {
            $all = $this->totemRepository->findAllOrdered();

            $candidates = [];
            foreach ($all as $candidate) {
                if ($candidate->getId() !== self::DEFAULT_TOTEM_ID) {
                    $candidates[] = $candidate;
                }
            }

            if ($candidates === []) {
                $totem = $this->totemRepository->getById(self::DEFAULT_TOTEM_ID);
            } else {
                $totem = $candidates[random_int(0, \count($candidates) - 1)];
            }

            if ($totem === null) {
                throw new TotemNotFoundException('Default Egg totem not found (id ' . self::DEFAULT_TOTEM_ID . ').');
            }
        }

        if ($totem === null) {
            $totem = $this->totemRepository->getById(self::DEFAULT_TOTEM_ID);
            if ($totem === null) {
                throw new TotemNotFoundException('Default Egg totem not found (id ' . self::DEFAULT_TOTEM_ID . ').');
            }
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

    public function getAuthorOrFail(int $authorId): Author
    {
        $author = $this->authorRepository->getById($authorId);

        if ($author === null) {
            throw new AuthorNotFoundException('Author not found for id ' . $authorId . '.');
        }

        return $author;
    }

    /**
     * @return Author[]
     */
    public function listAll(): array
    {
        return $this->authorRepository->findAll();
    }

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

    public function deleteAuthor(int $authorId): void
    {
        $author = $this->getAuthorOrFail($authorId);

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
