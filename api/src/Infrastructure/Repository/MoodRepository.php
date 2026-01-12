<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Mood;
use App\Domain\Repository\MoodRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use InvalidArgumentException;

/**
 * @extends ServiceEntityRepository<Mood>
 */
final class MoodRepository extends ServiceEntityRepository implements MoodRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mood::class);
    }

    public function getById(int $id): ?Mood
    {
        /** @var Mood|null $mood */
        $mood = $this->find($id);

        return $mood;
    }

    public function findAllOrdered(): array
    {
        /** @var Mood[] $moods */
        $moods = $this->findBy([], ['label' => 'ASC']);

        return $moods;
    }

    public function findPage(int $limit, int $offset, string $sort, string $direction): array
    {
        $sortMap = [
            'id' => 'm.id',
            'key' => 'm.key',
            'label' => 'm.label',
        ];

        $sortField = $sortMap[$sort] ?? $sortMap['label'];

        /** @var Mood[] $moods */
        $moods = $this->createQueryBuilder('m')
            ->orderBy($sortField, $direction)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return $moods;
    }

    public function countAll(): int
    {
        $qb = $this->createQueryBuilder('m')
            ->select('COUNT(m.id)');

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function getByKey(string $key): ?Mood
    {
        /** @var Mood|null $mood */
        $mood = $this->findOneBy(['key' => $key]);

        return $mood;
    }

    public function save(object $mood): void
    {
        if (!$mood instanceof Mood) {
            throw new InvalidArgumentException('Expected instance of Mood.');
        }

        $em = $this->getEntityManager();
        $em->persist($mood);
        $em->flush();
    }

    public function delete(object $mood): void
    {
        if (!$mood instanceof Mood) {
            throw new InvalidArgumentException('Expected instance of Mood.');
        }

        $em = $this->getEntityManager();
        $em->remove($mood);
        $em->flush();
    }
}
