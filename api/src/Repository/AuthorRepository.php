<?php

namespace App\Repository;

use App\Entity\Author;
use App\Enum\MoodColor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Author>
 *
 * Handles Author persistence and selection queries.
 */
class AuthorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }

    /**
     * Finds authors matching a specific mood color.
     *
     * @return Author[]
     */
    public function findByMood(MoodColor $moodColor): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.moodColor = :m')
            ->setParameter('m', $moodColor)
            ->getQuery()
            ->getResult();
    }

    /**
     * Searches authors whose pseudo contains the given query.
     *
     * @return Author[]
     */
    public function searchByPseudo(string $query): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.pseudo LIKE :q')
            ->setParameter('q', '%' . $query . '%')
            ->getQuery()
            ->getResult();
    }
}
