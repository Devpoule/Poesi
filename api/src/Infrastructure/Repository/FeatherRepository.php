<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Feather;
use App\Domain\Repository\FeatherRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use InvalidArgumentException;

/**
 * @extends ServiceEntityRepository<Feather>
 */
final class FeatherRepository extends ServiceEntityRepository implements FeatherRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Feather::class);
    }

    public function getById(int $id): ?Feather
    {
        /** @var Feather|null $feather */
        $feather = $this->find($id);

        return $feather;
    }

    public function findAllOrdered(): array
    {
        /** @var Feather[] $feathers */
        $feathers = $this->findBy([], ['label' => 'ASC']);

        return $feathers;
    }

    public function getByKey(string $key): ?Feather
    {
        /** @var Feather|null $feather */
        $feather = $this->findOneBy(['key' => $key]);

        return $feather;
    }

    public function save(object $feather): void
    {
        if (!$feather instanceof Feather) {
            throw new InvalidArgumentException('Expected instance of Feather.');
        }

        $em = $this->getEntityManager();
        $em->persist($feather);
        $em->flush();
    }

    public function delete(object $feather): void
    {
        if (!$feather instanceof Feather) {
            throw new InvalidArgumentException('Expected instance of Feather.');
        }

        $em = $this->getEntityManager();
        $em->remove($feather);
        $em->flush();
    }
}
