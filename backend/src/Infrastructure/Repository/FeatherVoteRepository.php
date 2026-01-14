<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\FeatherVote;
use App\Domain\Entity\Poem;
use App\Domain\Entity\User;
use App\Domain\Repository\FeatherVoteRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use InvalidArgumentException;

/**
 * Doctrine implementation of FeatherVoteRepositoryInterface.
 *
 * @extends ServiceEntityRepository<FeatherVote>
 */
final class FeatherVoteRepository extends ServiceEntityRepository implements FeatherVoteRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FeatherVote::class);
    }

    /**
     * Retrieve a FeatherVote by id.
     *
     * @param int $id
     *
     * @return FeatherVote|null
     */
    public function getById(int $id): ?FeatherVote
    {
        /** @var FeatherVote|null $vote */
        $vote = $this->find($id);

        return $vote;
    }

    /**
     * @return FeatherVote[]
     */
    public function findAll(): array
    {
        /** @var FeatherVote[] $rows */
        $rows = parent::findBy([], ['updatedAt' => 'DESC']);

        return $rows;
    }

    public function findListPage(int $limit, int $offset, string $sort, string $direction): array
    {
        return $this->buildListQuery($sort, $direction)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getArrayResult();
    }

    public function findListByPoemPage(Poem $poem, int $limit, int $offset, string $sort, string $direction): array
    {
        return $this->buildListQuery($sort, $direction)
            ->andWhere('v.poem = :poem')
            ->setParameter('poem', $poem)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getArrayResult();
    }

    public function findListByVoterPage(User $voter, int $limit, int $offset, string $sort, string $direction): array
    {
        return $this->buildListQuery($sort, $direction)
            ->andWhere('v.voter = :voter')
            ->setParameter('voter', $voter)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getArrayResult();
    }

    public function countAll(): int
    {
        $qb = $this->createQueryBuilder('v')
            ->select('COUNT(v.id)');

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function countByPoem(Poem $poem): int
    {
        $qb = $this->createQueryBuilder('v')
            ->select('COUNT(v.id)')
            ->andWhere('v.poem = :poem')
            ->setParameter('poem', $poem);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function countByVoter(User $voter): int
    {
        $qb = $this->createQueryBuilder('v')
            ->select('COUNT(v.id)')
            ->andWhere('v.voter = :voter')
            ->setParameter('voter', $voter);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param Poem $poem
     *
     * @return FeatherVote[]
     */
    public function findByPoem(Poem $poem): array
    {
        /** @var FeatherVote[] $rows */
        $rows = $this->findBy(['poem' => $poem], ['updatedAt' => 'DESC']);

        return $rows;
    }

    /**
     * @param User $voter
     *
     * @return FeatherVote[]
     */
    public function findByVoter(User $voter): array
    {
        /** @var FeatherVote[] $rows */
        $rows = $this->findBy(['voter' => $voter], ['updatedAt' => 'DESC']);

        return $rows;
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function buildListQuery(string $sort, string $direction)
    {
        $sortMap = [
            'id' => 'v.id',
            'createdAt' => 'v.createdAt',
            'updatedAt' => 'v.updatedAt',
        ];

        $sortField = $sortMap[$sort] ?? $sortMap['updatedAt'];

        return $this->createQueryBuilder('v')
            ->leftJoin('v.voter', 'u')
            ->leftJoin('v.poem', 'p')
            ->select(
                'v.id',
                'v.featherType',
                'v.createdAt',
                'v.updatedAt',
                'u.id AS voterId',
                'u.pseudo AS voterPseudo',
                'p.id AS poemId',
                'p.title AS poemTitle'
            )
            ->orderBy($sortField, $direction);
    }

    /**
     * @param User $voter
     * @param Poem   $poem
     *
     * @return FeatherVote|null
     */
    public function findOneByVoterAndPoem(User $voter, Poem $poem): ?FeatherVote
    {
        /** @var FeatherVote|null $row */
        $row = $this->findOneBy(['voter' => $voter, 'poem' => $poem]);

        return $row;
    }

    /**
     * @param object $vote
     *
     * @return void
     */
    public function save(object $vote): void
    {
        if (!$vote instanceof FeatherVote) {
            throw new InvalidArgumentException('Expected instance of FeatherVote.');
        }

        $em = $this->getEntityManager();
        $em->persist($vote);
        $em->flush();
    }

    /**
     * @param object $vote
     *
     * @return void
     */
    public function delete(object $vote): void
    {
        if (!$vote instanceof FeatherVote) {
            throw new InvalidArgumentException('Expected instance of FeatherVote.');
        }

        $em = $this->getEntityManager();
        $em->remove($vote);
        $em->flush();
    }
}
