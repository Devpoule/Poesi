<?php

namespace App\Repository;

use App\Entity\Totem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Totem>
 *
 * Handles Totem persistence and retrieval.
 */
class TotemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Totem::class);
    }

    /**
     * Finds a totem by its name (case-insensitive).
     */
    public function findByName(string $name): ?Totem
    {
        return $this->createQueryBuilder('t')
            ->andWhere('LOWER(t.name) = LOWER(:name)')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
