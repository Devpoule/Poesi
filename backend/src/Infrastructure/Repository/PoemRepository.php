<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Poem;
use App\Domain\Entity\User;
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

    public function findListPage(int $limit, int $offset, string $sort, string $direction): array
    {
        $sortMap = [
            'id' => 'p.id',
            'title' => 'p.title',
            'status' => 'p.status',
            'createdAt' => 'p.createdAt',
            'publishedAt' => 'p.publishedAt',
        ];

        $sortField = $sortMap[$sort] ?? $sortMap['createdAt'];

        $qb = $this->createQueryBuilder('p');

        $qb
            ->leftJoin('p.author', 'u')
            ->leftJoin('u.totem', 't')
            ->select(
                'p.id',
                'p.title',
                'p.status',
                'p.moodColor',
                'p.symbolType',
                'p.createdAt',
                'p.publishedAt',
                'u.id AS authorId',
                'u.pseudo AS authorPseudo',
                't.id AS authorTotemId'
            )
            ->orderBy($sortField, $direction)
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        /** @var list<array{
         *     id:int,
         *     title:string,
         *     status:mixed,
         *     moodColor:mixed,
         *     symbolType:mixed,
         *     createdAt:\DateTimeImmutable,
         *     publishedAt:\DateTimeImmutable|null,
         *     authorId:int|null,
         *     authorPseudo:string|null,
         *     authorTotemId:int|null
         * }> $rows
         */
        $rows = $qb->getQuery()->getArrayResult();

        return $rows;
    }

    public function findFullPage(int $limit, int $offset, string $sort, string $direction): array
    {
        $sortMap = [
            'id' => 'p.id',
            'title' => 'p.title',
            'status' => 'p.status',
            'createdAt' => 'p.createdAt',
            'publishedAt' => 'p.publishedAt',
        ];

        $sortField = $sortMap[$sort] ?? $sortMap['createdAt'];

        $idRows = $this->createQueryBuilder('p')
            ->select('p.id')
            ->orderBy($sortField, $direction)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getScalarResult();

        if ($idRows === []) {
            return [];
        }

        $ids = array_map(
            static fn (array $row): int => (int) $row['id'],
            $idRows
        );

        /** @var Poem[] $rows */
        $rows = $this->createQueryBuilder('p')
            ->leftJoin('p.author', 'u')
            ->addSelect('u')
            ->leftJoin('u.totem', 't')
            ->addSelect('t')
            ->leftJoin('p.featherVotes', 'fv')
            ->addSelect('fv')
            ->andWhere('p.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->distinct()
            ->getQuery()
            ->getResult();

        $byId = [];
        foreach ($rows as $poem) {
            $id = $poem->getId();
            if ($id !== null) {
                $byId[$id] = $poem;
            }
        }

        $ordered = [];
        foreach ($ids as $id) {
            if (isset($byId[$id])) {
                $ordered[] = $byId[$id];
            }
        }

        return $ordered;
    }

    public function countAll(): int
    {
        $qb = $this->createQueryBuilder('p')
            ->select('COUNT(p.id)');

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Retrieve all poems for a given user ordered by creation date (newest first).
     *
     * @param User $user
     *
     * @return Poem[]
     */
    public function findByUser(User $user): array
    {
        /** @var Poem[] $poems */
        $poems = $this->findBy(
            ['author' => $user],
            ['createdAt' => 'DESC']
        );

        return $poems;
    }

    /**
     * Retrieve all published poems for a given user ordered by publication date (newest first).
     *
     * @param User $user
     *
     * @return Poem[]
     */
    public function findPublishedByUser(User $user): array
    {
        $qb = $this->createQueryBuilder('p');

        $qb
            ->andWhere('p.author = :author')
            ->andWhere('p.status = :status')
            ->setParameter('author', $user)
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
