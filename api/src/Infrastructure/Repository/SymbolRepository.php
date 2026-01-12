<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Symbol;
use App\Domain\Repository\SymbolRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use InvalidArgumentException;

/**
 * @extends ServiceEntityRepository<Symbol>
 */
final class SymbolRepository extends ServiceEntityRepository implements SymbolRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Symbol::class);
    }

    public function getById(int $id): ?Symbol
    {
        /** @var Symbol|null $symbol */
        $symbol = $this->find($id);

        return $symbol;
    }

    public function findAllOrdered(): array
    {
        /** @var Symbol[] $symbols */
        $symbols = $this->findBy([], ['label' => 'ASC']);

        return $symbols;
    }

    public function findPage(int $limit, int $offset, string $sort, string $direction): array
    {
        $sortMap = [
            'id' => 's.id',
            'key' => 's.key',
            'label' => 's.label',
        ];

        $sortField = $sortMap[$sort] ?? $sortMap['label'];

        /** @var Symbol[] $symbols */
        $symbols = $this->createQueryBuilder('s')
            ->orderBy($sortField, $direction)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return $symbols;
    }

    public function countAll(): int
    {
        $qb = $this->createQueryBuilder('s')
            ->select('COUNT(s.id)');

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function getByKey(string $key): ?Symbol
    {
        /** @var Symbol|null $symbol */
        $symbol = $this->findOneBy(['key' => $key]);

        return $symbol;
    }

    public function save(object $symbol): void
    {
        if (!$symbol instanceof Symbol) {
            throw new InvalidArgumentException('Expected instance of Symbol.');
        }

        $em = $this->getEntityManager();
        $em->persist($symbol);
        $em->flush();
    }

    public function delete(object $symbol): void
    {
        if (!$symbol instanceof Symbol) {
            throw new InvalidArgumentException('Expected instance of Symbol.');
        }

        $em = $this->getEntityManager();
        $em->remove($symbol);
        $em->flush();
    }
}
