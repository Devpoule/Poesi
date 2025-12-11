<?php

namespace App\Domain\Service;

use App\Domain\Entity\Poem;
use App\Domain\Enum\MoodColor;
use App\Domain\Enum\PoemStatus;
use App\Domain\Exception\AuthorNotFoundException;
use App\Domain\Exception\PoemNotFoundException;
use App\Domain\Repository\AuthorRepositoryInterface;
use App\Domain\Repository\PoemRepositoryInterface;

/**
 * Domain service handling poem creation and lifecycle transitions.
 */
class PoemService
{
    public function __construct(
        private PoemRepositoryInterface $poemRepository,
        private AuthorRepositoryInterface $authorRepository,
    ) {
    }

    /**
     * Create a new draft poem for the given author.
     *
     * @param int       $authorId
     * @param string    $title
     * @param string    $content
     * @param MoodColor $moodColor
     *
     * @return Poem
     */
    public function createDraft(
        int $authorId,
        string $title,
        string $content,
        MoodColor $moodColor
    ): Poem {
        $author = $this->authorRepository->getById($authorId);

        if ($author === null) {
            throw new AuthorNotFoundException('Author not found for id ' . $authorId . '.');
        }

        $poem = new Poem();
        $poem->setAuthor($author);
        $poem->setTitle($title);
        $poem->setContent($content);
        $poem->setMoodColor($moodColor);
        $poem->setStatus(PoemStatus::DRAFT);

        $this->poemRepository->save($poem);

        return $poem;
    }

    /**
     * Publish a poem and set the publication date.
     *
     * @param int $poemId
     *
     * @return Poem
     */
    public function publish(int $poemId): Poem
    {
        $poem = $this->getPoemOrFail($poemId);

        $poem->setStatus(PoemStatus::PUBLISHED);
        $poem->setPublishedAt(new \DateTimeImmutable());

        $this->poemRepository->save($poem);

        return $poem;
    }

    /**
     * Retrieve a poem by id or throw an exception if not found.
     *
     * @param int $poemId
     *
     * @return Poem
     */
    public function getPoemOrFail(int $poemId): Poem
    {
        $poem = $this->poemRepository->getById($poemId);

        if ($poem === null) {
            throw new PoemNotFoundException('Poem not found for id ' . $poemId . '.');
        }

        return $poem;
    }

    /**
     * Return all poems.
     *
     * @return Poem[]
     */
    public function listAll(): array
    {
        return $this->poemRepository->findAll();
    }

    /**
     * Return all poems for a given author ordered by creation date.
     *
     * @param int $authorId
     *
     * @return Poem[]
     */
    public function listPoemsForAuthor(int $authorId): array
    {
        $author = $this->authorRepository->getById($authorId);

        if ($author === null) {
            throw new AuthorNotFoundException('Author not found for id ' . $authorId . '.');
        }

        return $this->poemRepository->findByAuthor($author);
    }

    /**
     * Return all published poems for a given author.
     *
     * @param int $authorId
     *
     * @return Poem[]
     */
    public function listPublishedForAuthor(int $authorId): array
    {
        $author = $this->authorRepository->getById($authorId);

        if ($author === null) {
            throw new AuthorNotFoundException('Author not found for id ' . $authorId . '.');
        }

        return $this->poemRepository->findPublishedByAuthor($author);
    }

    /**
     * Update the content and mood of an existing poem.
     * Only non-null fields are updated.
     *
     * @param int           $poemId
     * @param string|null   $title
     * @param string|null   $content
     * @param MoodColor|null $moodColor
     *
     * @return Poem
     */
    public function updatePoem(
        int $poemId,
        ?string $title = null,
        ?string $content = null,
        ?MoodColor $moodColor = null
    ): Poem {
        $poem = $this->getPoemOrFail($poemId);

        if ($title !== null) {
            $poem->setTitle($title);
        }

        if ($content !== null) {
            $poem->setContent($content);
        }

        if ($moodColor !== null) {
            $poem->setMoodColor($moodColor);
        }

        $this->poemRepository->save($poem);

        return $poem;
    }

    /**
     * Delete a poem by id.
     *
     * @param int $poemId
     *
     * @return void
     */
    public function deletePoem(int $poemId): void
    {
        $poem = $this->getPoemOrFail($poemId);

        $this->poemRepository->delete($poem);
    }
}
