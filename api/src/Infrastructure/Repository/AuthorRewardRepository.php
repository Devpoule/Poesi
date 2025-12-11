<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Author;
use App\Domain\Entity\AuthorReward;
use App\Domain\Repository\AuthorRewardRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use InvalidArgumentException;

/**
 * Doctrine implementation of AuthorRewardRepositoryInterface.
 *
 * @extends ServiceEntityRepository<AuthorReward>
 */
class AuthorRewardRepository extends ServiceEntityRepository implements AuthorRewardRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AuthorReward::class);
    }

    /**
     * Retrieve an AuthorReward by id.
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
     * Retrieve all author rewards.
     *
     * @return AuthorReward[]
     */
    public function findAll(): array
    {
        /** @var AuthorReward[] $authorRewards */
        $authorRewards = parent::findAll();

        return $authorRewards;
    }

    /**
     * Find all rewards assigned to a given author.
     *
     * @param Author $author
     *
     * @return AuthorReward[]
     */
    public function findByAuthor(Author $author): array
    {
        /** @var AuthorReward[] $authorRewards */
        $authorRewards = $this->findBy(
            ['author' => $author],
            ['earnedAt' => 'DESC']
        );

        return $authorRewards;
    }

    /**
     * Persist and flush the given AuthorReward.
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
     * Remove and flush the given AuthorReward.
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
