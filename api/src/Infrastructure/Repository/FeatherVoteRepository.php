<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Author;
use App\Domain\Entity\FeatherVote;
use App\Domain\Entity\Poem;
use App\Domain\Repository\FeatherVoteRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use InvalidArgumentException;

/**
 * Doctrine implementation of FeatherVoteRepositoryInterface.
 *
 * @extends ServiceEntityRepository<FeatherVote>
 */
class FeatherVoteRepository extends ServiceEntityRepository implements FeatherVoteRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FeatherVote::class);
    }

    /**
     * Retrieve a FeatherVote by its id.
     *
     * @param int $id
     *
     * @return FeatherVote|null
     */
    public function getById(int $id): ?FeatherVote
    {
        /** @var FeatherVote|null $vote */
        $vote = $this->find($id);

        return $vote;
    }

    /**
     * Retrieve all feather votes.
     *
     * @return FeatherVote[]
     */
    public function findAll(): array
    {
        /** @var FeatherVote[] $votes */
        $votes = parent::findAll();

        return $votes;
    }

    /**
     * Retrieve all votes for a given poem.
     *
     * @param Poem $poem
     *
     * @return FeatherVote[]
     */
    public function findByPoem(Poem $poem): array
    {
        /** @var FeatherVote[] $votes */
        $votes = $this->findBy(
            ['poem' => $poem],
            ['createdAt' => 'DESC']
        );

        return $votes;
    }

    /**
     * Retrieve all votes cast by a given author.
     *
     * @param Author $author
     *
     * @return FeatherVote[]
     */
    public function findByVoter(Author $author): array
    {
        /** @var FeatherVote[] $votes */
        $votes = $this->findBy(
            ['voter' => $author],
            ['createdAt' => 'DESC']
        );

        return $votes;
    }

    /**
     * Persist and flush the given FeatherVote.
     *
     * @param object $vote
     *
     * @return void
     */
    public function save(object $vote): void
    {
        if (!$vote instanceof FeatherVote) {
            throw new InvalidArgumentException('Expected instance of FeatherVote.');
        }

        $em = $this->getEntityManager();
        $em->persist($vote);
        $em->flush();
    }

    /**
     * Remove and flush the given FeatherVote.
     *
     * @param object $vote
     *
     * @return void
     */
    public function delete(object $vote): void
    {
        if (!$vote instanceof FeatherVote) {
            throw new InvalidArgumentException('Expected instance of FeatherVote.');
        }

        $em = $this->getEntityManager();
        $em->remove($vote);
        $em->flush();
    }
}
