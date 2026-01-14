<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Reward;
use App\Domain\Repository\RewardRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use InvalidArgumentException;

/**
 * Doctrine implementation of RewardRepositoryInterface.
 *
 * @extends ServiceEntityRepository<Reward>
 */
class RewardRepository extends ServiceEntityRepository implements RewardRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reward::class);
    }

    public function getById(int $id): ?Reward
    {
        /** @var Reward|null $reward */
        $reward = $this->find($id);

        return $reward;
    }

    public function findOneByCode(string $code): ?Reward
    {
        /** @var Reward|null $reward */
        $reward = $this->findOneBy(['code' => strtoupper($code)]);

        return $reward;
    }

    public function findAll(): array
    {
        /** @var Reward[] $rewards */
        $rewards = parent::findAll();

        return $rewards;
    }

    public function findPage(int $limit, int $offset, string $sort, string $direction): array
    {
        $sortMap = [
            'id' => 'r.id',
            'code' => 'r.code',
            'label' => 'r.label',
        ];

        $sortField = $sortMap[$sort] ?? $sortMap['id'];

        $qb = $this->createQueryBuilder('r')
            ->select('r.id', 'r.code', 'r.label')
            ->orderBy($sortField, $direction)
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        /** @var list<array{id:int,code:string,label:string}> $rows */
        $rows = $qb->getQuery()->getArrayResult();

        return $rows;
    }

    public function countAll(): int
    {
        $qb = $this->createQueryBuilder('r')
            ->select('COUNT(r.id)');

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function save(object $reward): void
    {
        if (!$reward instanceof Reward) {
            throw new InvalidArgumentException('Expected instance of Reward.');
        }

        $em = $this->getEntityManager();
        $em->persist($reward);
        $em->flush();
    }

    public function delete(object $reward): void
    {
        if (!$reward instanceof Reward) {
            throw new InvalidArgumentException('Expected instance of Reward.');
        }

        $em = $this->getEntityManager();
        $em->remove($reward);
        $em->flush();
    }
}
