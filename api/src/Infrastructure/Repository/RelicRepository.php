<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Relic;
use App\Domain\Repository\RelicRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use InvalidArgumentException;

/**
 * @extends ServiceEntityRepository<Relic>
 */
final class RelicRepository extends ServiceEntityRepository implements RelicRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Relic::class);
    }

    public function getById(int $id): ?Relic
    {
        /** @var Relic|null $relic */
        $relic = $this->find($id);

        return $relic;
    }

    public function findAllOrdered(): array
    {
        /** @var Relic[] $relics */
        $relics = $this->findBy([], ['rarity' => 'DESC', 'label' => 'ASC']);

        return $relics;
    }

    public function findPage(int $limit, int $offset, string $sort, string $direction): array
    {
        $sortMap = [
            'id' => 'r.id',
            'key' => 'r.key',
            'label' => 'r.label',
            'rarity' => 'r.rarity',
        ];

        $sortField = $sortMap[$sort] ?? $sortMap['rarity'];

        /** @var Relic[] $relics */
        $relics = $this->createQueryBuilder('r')
            ->orderBy($sortField, $direction)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return $relics;
    }

    public function countAll(): int
    {
        $qb = $this->createQueryBuilder('r')
            ->select('COUNT(r.id)');

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function getByKey(string $key): ?Relic
    {
        /** @var Relic|null $relic */
        $relic = $this->findOneBy(['key' => $key]);

        return $relic;
    }

    public function save(object $relic): void
    {
        if (!$relic instanceof Relic) {
            throw new InvalidArgumentException('Expected instance of Relic.');
        }

        $em = $this->getEntityManager();
        $em->persist($relic);
        $em->flush();
    }

    public function delete(object $relic): void
    {
        if (!$relic instanceof Relic) {
            throw new InvalidArgumentException('Expected instance of Relic.');
        }

        $em = $this->getEntityManager();
        $em->remove($relic);
        $em->flush();
    }
}
