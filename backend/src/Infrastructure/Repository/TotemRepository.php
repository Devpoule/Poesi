<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Totem;
use App\Domain\Repository\TotemRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use InvalidArgumentException;

/**
 * Doctrine implementation of TotemRepositoryInterface.
 *
 * @extends ServiceEntityRepository<Totem>
 */
final class TotemRepository extends ServiceEntityRepository implements TotemRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Totem::class);
    }

    public function getById(int $id): ?Totem
    {
        /** @var Totem|null $totem */
        $totem = $this->find($id);

        return $totem;
    }

    public function findAll(): array
    {
        /** @var Totem[] $totems */
        $totems = parent::findAll();

        return $totems;
    }

    public function findPage(int $limit, int $offset, string $sort, string $direction): array
    {
        $sortMap = [
            'id' => 't.id',
            'key' => 't.key',
            'name' => 't.name',
        ];

        $sortField = $sortMap[$sort] ?? $sortMap['name'];

        /** @var Totem[] $totems */
        $totems = $this->createQueryBuilder('t')
            ->orderBy($sortField, $direction)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return $totems;
    }

    public function countAll(): int
    {
        $qb = $this->createQueryBuilder('t')
            ->select('COUNT(t.id)');

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function getByKey(string $key): ?Totem
    {
        /** @var Totem|null $totem */
        $totem = $this->findOneBy(['key' => $key]);

        return $totem;
    }

    public function findAllOrdered(): array
    {
        /** @var Totem[] $totems */
        $totems = $this->findBy([], ['name' => 'ASC']);

        return $totems;
    }

    public function getRandomExcludingId(int $excludedId): ?Totem
    {
        $totems = $this->createQueryBuilder('t')
            ->andWhere('t.id != :excludedId')
            ->setParameter('excludedId', $excludedId)
            ->getQuery()
            ->getResult();

        if ($totems === []) {
            return null;
        }

        return $totems[array_rand($totems)];
    }

    public function save(object $totem): void
    {
        if (!$totem instanceof Totem) {
            throw new InvalidArgumentException('Expected instance of Totem.');
        }

        $em = $this->getEntityManager();
        $em->persist($totem);
        $em->flush();
    }

    public function delete(object $totem): void
    {
        if (!$totem instanceof Totem) {
            throw new InvalidArgumentException('Expected instance of Totem.');
        }

        $em = $this->getEntityManager();
        $em->remove($totem);
        $em->flush();
    }
}
