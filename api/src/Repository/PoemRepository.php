<?php

namespace App\Repository;

use App\Entity\Author;
use App\Entity\Poem;
use App\Enum\PoemStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Poem>
 *
 * Handles poem lookup and publication-related queries.
 */
class PoemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Poem::class);
    }

    /**
     * Finds published poems ordered by publication date (newest first).
     *
     * @return Poem[]
     */
    public function findPublished(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.status = :status')
            ->setParameter('status', PoemStatus::PUBLISHED)
            ->orderBy('p.publishedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Finds all poems written by a specific author.
     *
     * @return Poem[]
     */
    public function findByAuthor(Author $author): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.author = :a')
            ->setParameter('a', $author)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Counts poems published by a specific author.
     */
    public function countPublishedByAuthor(Author $author): int
    {
        return (int) $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->andWhere('p.author = :a')
            ->andWhere('p.status = :status')
            ->setParameter('a', $author)
            ->setParameter('status', PoemStatus::PUBLISHED)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
