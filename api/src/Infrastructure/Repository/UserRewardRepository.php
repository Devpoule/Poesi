<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\User;
use App\Domain\Entity\UserReward;
use App\Domain\Entity\Reward;
use App\Domain\Repository\UserRewardRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use InvalidArgumentException;

/**
 * Doctrine implementation of UserRewardRepositoryInterface.
 *
 * @extends ServiceEntityRepository<UserReward>
 */
final class UserRewardRepository extends ServiceEntityRepository implements UserRewardRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserReward::class);
    }

    /**
     * Retrieve a UserReward by its id.
     *
     * @param int $id
     *
     * @return UserReward|null
     */
    public function getById(int $id): ?UserReward
    {
        /** @var UserReward|null $userReward */
        $userReward = $this->find($id);

        return $userReward;
    }

    /**
     * @param User $user
     *
     * @return UserReward[]
     */
    public function findByUser(User $user): array
    {
        /** @var UserReward[] $rows */
        $rows = $this->findBy(['user' => $user], ['createdAt' => 'DESC']);

        return $rows;
    }

    /**
     * @param User $user
     * @param Reward $reward
     *
     * @return UserReward|null
     */
    public function findOneByUserAndReward(User $user, Reward $reward): ?UserReward
    {
        /** @var UserReward|null $row */
        $row = $this->findOneBy(['user' => $user, 'reward' => $reward]);

        return $row;
    }

    /**
     * Persist and flush a UserReward.
     *
     * @param object $userReward
     *
     * @return void
     */
    public function save(object $userReward): void
    {
        if (!$userReward instanceof UserReward) {
            throw new InvalidArgumentException('Expected instance of UserReward.');
        }

        $em = $this->getEntityManager();
        $em->persist($userReward);
        $em->flush();
    }

    /**
     * Remove and flush a UserReward.
     *
     * @param object $userReward
     *
     * @return void
     */
    public function delete(object $userReward): void
    {
        if (!$userReward instanceof UserReward) {
            throw new InvalidArgumentException('Expected instance of UserReward.');
        }

        $em = $this->getEntityManager();
        $em->remove($userReward);
        $em->flush();
    }
}
