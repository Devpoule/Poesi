<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Totem;
use App\Domain\Repository\TotemRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use InvalidArgumentException;

/**
 * Doctrine implementation of TotemRepositoryInterface.
 *
 * @extends ServiceEntityRepository<Totem>
 */
class TotemRepository extends ServiceEntityRepository implements TotemRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Totem::class);
    }

    /**
     * Retrieve a Totem by id.
     *
     * @param int $id
     *
     * @return Totem|null
     */
    public function getById(int $id): ?Totem
    {
        /** @var Totem|null $totem */
        $totem = $this->find($id);

        return $totem;
    }

    /**
     * Retrieve all totems.
     *
     * @return Totem[]
     */
    public function findAll(): array
    {
        /** @var Totem[] $totems */
        $totems = parent::findAll();

        return $totems;
    }

    /**
     * Retrieve all totems ordered by name ascending.
     *
     * @return Totem[]
     */
    public function findAllOrdered(): array
    {
        /** @var Totem[] $totems */
        $totems = $this->findBy([], ['name' => 'ASC']);

        return $totems;
    }

    /**
     * Persist and flush the given Totem.
     *
     * @param object $totem
     *
     * @return void
     */
    public function save(object $totem): void
    {
        if (!$totem instanceof Totem) {
            throw new InvalidArgumentException('Expected instance of Totem.');
        }

        $em = $this->getEntityManager();
        $em->persist($totem);
        $em->flush();
    }

    /**
     * Remove and flush the given Totem.
     *
     * @param object $totem
     *
     * @return void
     */
    public function delete(object $totem): void
    {
        if (!$totem instanceof Totem) {
            throw new InvalidArgumentException('Expected instance of Totem.');
        }

        $em = $this->getEntityManager();
        $em->remove($totem);
        $em->flush();
    }
}
