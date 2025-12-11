<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\User;
use App\Domain\Repository\UserRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use InvalidArgumentException;

/**
 * Doctrine implementation of UserRepositoryInterface.
 *
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements UserRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Retrieve a User by id.
     *
     * @param int $id
     *
     * @return User|null
     */
    public function getById(int $id): ?User
    {
        /** @var User|null $user */
        $user = $this->find($id);

        return $user;
    }

    /**
     * Retrieve all users.
     *
     * @return User[]
     */
    public function findAll(): array
    {
        /** @var User[] $users */
        $users = parent::findAll();

        return $users;
    }

    /**
     * Find a user by email.
     *
     * @param string $email
     *
     * @return User|null
     */
    public function findOneByEmail(string $email): ?User
    {
        /** @var User|null $user */
        $user = $this->findOneBy(['email' => $email]);

        return $user;
    }

    /**
     * Persist and flush the given User.
     *
     * @param object $user
     *
     * @return void
     */
    public function save(object $user): void
    {
        if (!$user instanceof User) {
            throw new InvalidArgumentException('Expected instance of User.');
        }

        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush();
    }

    /**
     * Remove and flush the given User.
     *
     * @param object $user
     *
     * @return void
     */
    public function delete(object $user): void
    {
        if (!$user instanceof User) {
            throw new InvalidArgumentException('Expected instance of User.');
        }

        $em = $this->getEntityManager();
        $em->remove($user);
        $em->flush();
    }
}
