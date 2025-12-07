<?php

namespace App\Repository;

use App\Entity\Reward;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reward>
 *
 * Handles reward lookup and persistence.
 */
class RewardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reward::class);
    }

    /**
     * Finds a reward by its code.
     */
    public function findByCode(string $code): ?Reward
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.code = :c')
            ->setParameter('c', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
