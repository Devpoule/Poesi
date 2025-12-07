<?php

namespace App\Repository;

use App\Entity\Author;
use App\Entity\FeatherVote;
use App\Entity\Poem;
use App\Enum\FeatherType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FeatherVote>
 *
 * Handles vote registration and counting.
 */
class FeatherVoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FeatherVote::class);
    }

    /**
     * Checks if a voter has already voted for a given poem.
     */
    public function findVote(Author $voter, Poem $poem): ?FeatherVote
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.voter = :voter')
            ->andWhere('v.poem = :poem')
            ->setParameter('voter', $voter)
            ->setParameter('poem', $poem)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Counts how many feathers of a certain type a poem received.
     */
    public function countFeathers(Poem $poem, FeatherType $type): int
    {
        return (int) $this->createQueryBuilder('v')
            ->select('COUNT(v.id)')
            ->andWhere('v.poem = :poem')
            ->andWhere('v.featherType = :type')
            ->setParameter('poem', $poem)
            ->setParameter('type', $type)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
