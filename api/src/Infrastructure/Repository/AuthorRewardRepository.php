<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Author;
use App\Domain\Entity\AuthorReward;
use App\Domain\Entity\Reward;
use App\Domain\Repository\AuthorRewardRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use InvalidArgumentException;

/**
 * Doctrine implementation of AuthorRewardRepositoryInterface.
 *
 * @extends ServiceEntityRepository<AuthorReward>
 */
final class AuthorRewardRepository extends ServiceEntityRepository implements AuthorRewardRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AuthorReward::class);
    }

    /**
     * Retrieve an AuthorReward by its id.
     *
     * @param int $id
     *
     * @return AuthorReward|null
     */
    public function getById(int $id): ?AuthorReward
    {
        /** @var AuthorReward|null $authorReward */
        $authorReward = $this->find($id);

        return $authorReward;
    }

    /**
     * @param Author $author
     *
     * @return AuthorReward[]
     */
    public function findByAuthor(Author $author): array
    {
        /** @var AuthorReward[] $rows */
        $rows = $this->findBy(['author' => $author], ['createdAt' => 'DESC']);

        return $rows;
    }

    /**
     * @param Author $author
     * @param Reward $reward
     *
     * @return AuthorReward|null
     */
    public function findOneByAuthorAndReward(Author $author, Reward $reward): ?AuthorReward
    {
        /** @var AuthorReward|null $row */
        $row = $this->findOneBy(['author' => $author, 'reward' => $reward]);

        return $row;
    }

    /**
     * Persist and flush an AuthorReward.
     *
     * @param object $authorReward
     *
     * @return void
     */
    public function save(object $authorReward): void
    {
        if (!$authorReward instanceof AuthorReward) {
            throw new InvalidArgumentException('Expected instance of AuthorReward.');
        }

        $em = $this->getEntityManager();
        $em->persist($authorReward);
        $em->flush();
    }

    /**
     * Remove and flush an AuthorReward.
     *
     * @param object $authorReward
     *
     * @return void
     */
    public function delete(object $authorReward): void
    {
        if (!$authorReward instanceof AuthorReward) {
            throw new InvalidArgumentException('Expected instance of AuthorReward.');
        }

        $em = $this->getEntityManager();
        $em->remove($authorReward);
        $em->flush();
    }
}
