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

    public function findAllOrdered(): array
    {
        /** @var Totem[] $totems */
        $totems = $this->findBy([], ['name' => 'ASC']);

        return $totems;
    }

    public function getRandomExcludingId(int $excludedId): ?Totem
    {
        /** @var Totem|null $totem */
        $totem = $this->createQueryBuilder('t')
            ->andWhere('t.id != :excludedId')
            ->setParameter('excludedId', $excludedId)
            ->orderBy('RAND()')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $totem;
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
