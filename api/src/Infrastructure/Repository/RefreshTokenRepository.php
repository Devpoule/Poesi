<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\RefreshToken;
use App\Domain\Repository\RefreshTokenRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use InvalidArgumentException;

/**
 * @extends ServiceEntityRepository<RefreshToken>
 */
final class RefreshTokenRepository extends ServiceEntityRepository implements RefreshTokenRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RefreshToken::class);
    }

    public function getById(int $id): ?RefreshToken
    {
        /** @var RefreshToken|null $token */
        $token = $this->find($id);

        return $token;
    }

    /**
     * @return RefreshToken[]
     */
    public function findAll(): array
    {
        /** @var RefreshToken[] $tokens */
        $tokens = parent::findAll();

        return $tokens;
    }

    public function findOneByToken(string $token): ?RefreshToken
    {
        /** @var RefreshToken|null $row */
        $row = $this->findOneBy(['token' => $token]);

        return $row;
    }

    public function save(object $token): void
    {
        if (!$token instanceof RefreshToken) {
            throw new InvalidArgumentException('Expected instance of RefreshToken.');
        }

        $em = $this->getEntityManager();
        $em->persist($token);
        $em->flush();
    }

    public function delete(object $token): void
    {
        if (!$token instanceof RefreshToken) {
            throw new InvalidArgumentException('Expected instance of RefreshToken.');
        }

        $em = $this->getEntityManager();
        $em->remove($token);
        $em->flush();
    }
}
