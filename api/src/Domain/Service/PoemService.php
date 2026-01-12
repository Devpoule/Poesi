<?php

namespace App\Domain\Service;

use App\Domain\Entity\Poem;
use App\Domain\Enum\MoodColor;
use App\Domain\Enum\PoemStatus;
use App\Domain\Exception\CannotUpdate\CannotUpdatePoemException;
use App\Domain\Exception\NotFound\UserNotFoundException;
use App\Domain\Exception\CannotDelete\CannotDeletePoemWithVotesException;
use App\Domain\Exception\CannotPublish\CannotPublishPoemException;
use App\Domain\Exception\NotFound\PoemNotFoundException;
use App\Domain\Repository\PoemRepositoryInterface;
use App\Domain\Repository\UserRepositoryInterface;

/**
 * Domain service handling poem creation and lifecycle transitions.
 */
class PoemService
{
    private const DEFAULT_TOTEM_ID = 1;

    public function __construct(
        private PoemRepositoryInterface $poemRepository,
        private UserRepositoryInterface $userRepository,
    ) {
    }

    /**
     * Create a new draft poem for the given author.
     *
     * @param int       $userId
     * @param string    $title
     * @param string    $content
     * @param MoodColor $moodColor
     *
     * @return Poem
     */
    public function createDraft(
        int $userId,
        string $title,
        string $content,
        MoodColor $moodColor
    ): Poem {
        $user = $this->userRepository->getById($userId);

        if ($user === null) {
            throw new UserNotFoundException('User not found for id ' . $userId . '.');
        }

        $poem = new Poem();
        $poem->setAuthor($user);
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

        $user = $poem->getAuthor();
        $userTotemId = $user?->getTotem()?->getId();

        if ($userTotemId === null || $userTotemId === self::DEFAULT_TOTEM_ID) {
            throw new CannotPublishPoemException(
                'Cannot publish poem: user must choose a totem before publishing.'
            );
        }

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
     * @return list<array<string, mixed>>
     */
    public function listPage(int $limit, int $offset, string $sort, string $direction): array
    {
        return $this->poemRepository->findListPage($limit, $offset, $sort, $direction);
    }

    /**
     * @return Poem[]
     */
    public function listFullPage(int $limit, int $offset, string $sort, string $direction): array
    {
        return $this->poemRepository->findFullPage($limit, $offset, $sort, $direction);
    }

    public function countPoems(): int
    {
        return $this->poemRepository->countAll();
    }

    /**
     * Return all poems for a given user ordered by creation date.
     *
     * @param int $userId
     *
     * @return Poem[]
     */
    public function listPoemsForUser(int $userId): array
    {
        $user = $this->userRepository->getById($userId);

        if ($user === null) {
            throw new UserNotFoundException('User not found for id ' . $userId . '.');
        }

        return $this->poemRepository->findByUser($user);
    }

    /**
     * Return all published poems for a given user.
     *
     * @param int $userId
     *
     * @return Poem[]
     */
    public function listPublishedForUser(int $userId): array
    {
        $user = $this->userRepository->getById($userId);

        if ($user === null) {
            throw new UserNotFoundException('User not found for id ' . $userId . '.');
        }

        return $this->poemRepository->findPublishedByUser($user);
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

        if ($poem->getStatus() === PoemStatus::PUBLISHED) {
            throw new CannotUpdatePoemException('Cannot update a published poem.');
        }

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
     * A poem cannot be deleted if it still has feather votes attached.
     *
     * @param int $poemId
     *
     * @return void
     *
     * @throws PoemNotFoundException
     * @throws CannotDeletePoemWithVotesException
     */
    public function deletePoem(int $poemId): void
    {
        $poem = $this->getPoemOrFail($poemId);

        // Guard: prevent deletion if votes exist to keep referential integrity
        if ($poem->getFeatherVotes()->count() > 0) {
            throw new CannotDeletePoemWithVotesException(
                sprintf('Cannot delete poem %d because it still has feather votes.', $poemId)
            );
        }

        $this->poemRepository->delete($poem);
    }
}
