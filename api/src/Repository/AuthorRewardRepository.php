<?php

namespace App\Repository;

use App\Entity\Author;
use App\Entity\AuthorReward;
use App\Entity\Reward;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AuthorReward>
 *
 * Manages the association between authors and their rewards.
 */
class AuthorRewardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AuthorReward::class);
    }

    /**
     * Checks if an author already owns a specific reward.
     */
    public function hasReward(Author $author, Reward $reward): bool
    {
        return (bool) $this->createQueryBuilder('ar')
            ->select('COUNT(ar.id)')
            ->andWhere('ar.author = :a')
            ->andWhere('ar.reward = :r')
            ->setParameter('a', $author)
            ->setParameter('r', $reward)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Retrieves all rewards obtained by an author.
     *
     * @return AuthorReward[]
     */
    public function findByAuthor(Author $author): array
    {
        return $this->createQueryBuilder('ar')
            ->andWhere('ar.author = :a')
            ->setParameter('a', $author)
            ->orderBy('ar.earnedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
