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

    public function findAdminListPage(int $limit, int $offset, string $sort, string $direction): array
    {
        $sortMap = [
            'id' => 'u.id',
            'email' => 'u.email',
            'createdAt' => 'u.createdAt',
        ];

        $sortField = $sortMap[$sort] ?? $sortMap['createdAt'];

        $qb = $this->createQueryBuilder('u');

        $qb
            ->select(
                'u.id',
                'u.email',
                'u.pseudo',
                'u.roles',
                'u.createdAt',
                'u.lockedAt',
                'u.failedLoginAttempts'
            )
            ->orderBy($sortField, $direction)
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        /** @var list<array{
         *     id:int,
         *     email:string,
         *     pseudo:string|null,
         *     roles:string[],
         *     createdAt:\DateTimeImmutable,
         *     lockedAt:\DateTimeImmutable|null,
         *     failedLoginAttempts:int
         * }> $rows
         */
        $rows = $qb->getQuery()->getArrayResult();

        return $rows;
    }

    public function findOptionsPage(int $limit, int $offset, string $sort, string $direction): array
    {
        $sortMap = [
            'id' => 'u.id',
            'pseudo' => 'u.pseudo',
            'email' => 'u.email',
        ];

        $sortField = $sortMap[$sort] ?? $sortMap['id'];

        $qb = $this->createQueryBuilder('u');

        $qb
            ->select(
                'u.id',
                'u.pseudo',
                'u.email'
            )
            ->orderBy($sortField, $direction)
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        /** @var list<array{id:int,pseudo:string|null,email:string}> $rows */
        $rows = $qb->getQuery()->getArrayResult();

        return $rows;
    }

    public function findPublicListPage(int $limit, int $offset, string $sort, string $direction): array
    {
        $sortMap = [
            'id' => 'u.id',
            'pseudo' => 'u.pseudo',
            'moodColor' => 'u.moodColor',
        ];

        $sortField = $sortMap[$sort] ?? $sortMap['id'];

        $qb = $this->createQueryBuilder('u');

        $qb
            ->leftJoin('u.totem', 't')
            ->select(
                'u.id',
                'u.pseudo',
                'u.moodColor',
                't.id AS totemId'
            )
            ->orderBy($sortField, $direction)
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        /** @var list<array{id:int,pseudo:string|null,moodColor:string,totemId:int|null}> $rows */
        $rows = $qb->getQuery()->getArrayResult();

        return $rows;
    }

    public function countAll(): int
    {
        $qb = $this->createQueryBuilder('u')
            ->select('COUNT(u.id)');

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Retrieve admin list rows with minimal data.
     *
     * @return list<array{
     *     id:int,
     *     email:string,
     *     pseudo:string|null,
     *     roles:string[],
     *     createdAt:\DateTimeImmutable,
     *     lockedAt:\DateTimeImmutable|null,
     *     failedLoginAttempts:int
     * }>
     */
    public function findAdminList(): array
    {
        $qb = $this->createQueryBuilder('u');

        $qb
            ->select(
                'u.id',
                'u.email',
                'u.pseudo',
                'u.roles',
                'u.createdAt',
                'u.lockedAt',
                'u.failedLoginAttempts'
            )
            ->orderBy('u.createdAt', 'DESC');

        /** @var list<array{
         *     id:int,
         *     email:string,
         *     pseudo:string|null,
         *     roles:string[],
         *     createdAt:\DateTimeImmutable,
         *     lockedAt:\DateTimeImmutable|null,
         *     failedLoginAttempts:int
         * }> $rows
         */
        $rows = $qb->getQuery()->getArrayResult();

        return $rows;
    }

    /**
     * Retrieve lightweight user options for selectors.
     *
     * @return list<array{id:int,pseudo:string|null,email:string}>
     */
    public function findOptions(): array
    {
        $qb = $this->createQueryBuilder('u');

        $qb
            ->select(
                'u.id',
                'u.pseudo',
                'u.email'
            )
            ->orderBy('u.id', 'ASC');

        /** @var list<array{id:int,pseudo:string|null,email:string}> $rows */
        $rows = $qb->getQuery()->getArrayResult();

        return $rows;
    }

    /**
     * Retrieve public profile rows.
     *
     * @return list<array{id:int,pseudo:string|null,moodColor:string,totemId:int|null}>
     */
    public function findPublicList(): array
    {
        $qb = $this->createQueryBuilder('u');

        $qb
            ->leftJoin('u.totem', 't')
            ->select(
                'u.id',
                'u.pseudo',
                'u.moodColor',
                't.id AS totemId'
            )
            ->orderBy('u.id', 'ASC');

        /** @var list<array{id:int,pseudo:string|null,moodColor:string,totemId:int|null}> $rows */
        $rows = $qb->getQuery()->getArrayResult();

        return $rows;
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
