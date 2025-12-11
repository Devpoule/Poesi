<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Author;
use App\Domain\Repository\AuthorRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use InvalidArgumentException;

/**
 * Doctrine implementation of AuthorRepositoryInterface.
 *
 * @extends ServiceEntityRepository<Author>
 */
class AuthorRepository extends ServiceEntityRepository implements AuthorRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }

    /**
     * Retrieve an Author by its id.
     *
     * @param int $id
     *
     * @return Author|null
     */
    public function getById(int $id): ?Author
    {
        /** @var Author|null $author */
        $author = $this->find($id);

        return $author;
    }

    /**
     * Retrieve all authors.
     *
     * @return Author[]
     */
    public function findAll(): array
    {
        /** @var Author[] $authors */
        $authors = parent::findAll();

        return $authors;
    }

    /**
     * Find an Author by email.
     *
     * @param string $email
     *
     * @return Author|null
     */
    public function findOneByEmail(string $email): ?Author
    {
        /** @var Author|null $author */
        $author = $this->findOneBy(['email' => $email]);

        return $author;
    }

    /**
     * Persist and flush the given Author.
     *
     * @param object $author
     *
     * @return void
     */
    public function save(object $author): void
    {
        if (!$author instanceof Author) {
            throw new InvalidArgumentException('Expected instance of Author.');
        }

        $em = $this->getEntityManager();
        $em->persist($author);
        $em->flush();
    }

    /**
     * Remove and flush the given Author.
     *
     * @param object $author
     *
     * @return void
     */
    public function delete(object $author): void
    {
        if (!$author instanceof Author) {
            throw new InvalidArgumentException('Expected instance of Author.');
        }

        $em = $this->getEntityManager();
        $em->remove($author);
        $em->flush();
    }
}
