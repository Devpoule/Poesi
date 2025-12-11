<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Author;
use App\Domain\Entity\Poem;
use App\Domain\Entity\Author as DomainAuthor;
use App\Domain\Repository\PoemRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use InvalidArgumentException;

/**
 * Doctrine implementation of PoemRepositoryInterface.
 *
 * @extends ServiceEntityRepository<Poem>
 */
class PoemRepository extends ServiceEntityRepository implements PoemRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Poem::class);
    }

    /**
     * Retrieve a Poem by its id.
     *
     * @param int $id
     *
     * @return Poem|null
     */
    public function getById(int $id): ?Poem
    {
        /** @var Poem|null $poem */
        $poem = $this->find($id);

        return $poem;
    }

    /**
     * Retrieve all poems.
     *
     * @return Poem[]
     */
    public function findAll(): array
    {
        /** @var Poem[] $poems */
        $poems = parent::findAll();

        return $poems;
    }

    /**
     * Retrieve all poems for a given author ordered by creation date (newest first).
     *
     * @param Author $author
     *
     * @return Poem[]
     */
    public function findByAuthor(Author $author): array
    {
        /** @var Poem[] $poems */
        $poems = $this->findBy(
            ['author' => $author],
            ['createdAt' => 'DESC']
        );

        return $poems;
    }

    /**
     * Retrieve all published poems for a given author ordered by publication date (newest first).
     *
     * @param Author $author
     *
     * @return Poem[]
     */
    public function findPublishedByAuthor(Author $author): array
    {
        $qb = $this->createQueryBuilder('p');

        $qb
            ->andWhere('p.author = :author')
            ->andWhere('p.status = :status')
            ->setParameter('author', $author)
            ->setParameter('status', 'published')
            ->orderBy('p.publishedAt', 'DESC');

        /** @var Poem[] $poems */
        $poems = $qb->getQuery()->getResult();

        return $poems;
    }

    /**
     * Persist and flush the given Poem.
     *
     * @param object $poem
     *
     * @return void
     */
    public function save(object $poem): void
    {
        if (!$poem instanceof Poem) {
            throw new InvalidArgumentException('Expected instance of Poem.');
        }

        $em = $this->getEntityManager();
        $em->persist($poem);
        $em->flush();
    }

    /**
     * Remove and flush the given Poem.
     *
     * @param object $poem
     *
     * @return void
     */
    public function delete(object $poem): void
    {
        if (!$poem instanceof Poem) {
            throw new InvalidArgumentException('Expected instance of Poem.');
        }

        $em = $this->getEntityManager();
        $em->remove($poem);
        $em->flush();
    }
}
