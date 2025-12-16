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
final class FeatherVoteRepository extends ServiceEntityRepository implements FeatherVoteRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FeatherVote::class);
    }

    /**
     * Retrieve a FeatherVote by id.
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
     * @return FeatherVote[]
     */
    public function findAll(): array
    {
        /** @var FeatherVote[] $rows */
        $rows = parent::findBy([], ['updatedAt' => 'DESC']);

        return $rows;
    }

    /**
     * @param Poem $poem
     *
     * @return FeatherVote[]
     */
    public function findByPoem(Poem $poem): array
    {
        /** @var FeatherVote[] $rows */
        $rows = $this->findBy(['poem' => $poem], ['updatedAt' => 'DESC']);

        return $rows;
    }

    /**
     * @param Author $voter
     *
     * @return FeatherVote[]
     */
    public function findByVoter(Author $voter): array
    {
        /** @var FeatherVote[] $rows */
        $rows = $this->findBy(['voter' => $voter], ['updatedAt' => 'DESC']);

        return $rows;
    }

    /**
     * @param Author $voter
     * @param Poem   $poem
     *
     * @return FeatherVote|null
     */
    public function findOneByVoterAndPoem(Author $voter, Poem $poem): ?FeatherVote
    {
        /** @var FeatherVote|null $row */
        $row = $this->findOneBy(['voter' => $voter, 'poem' => $poem]);

        return $row;
    }

    /**
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
