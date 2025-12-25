<?php

namespace App\Domain\Entity;

use App\Infrastructure\Repository\AuthorRewardRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents the association between an Author and a Reward.
 */
#[ORM\Entity(repositoryClass: AuthorRewardRepository::class)]
#[ORM\Table(name: 'author_reward')]
#[ORM\UniqueConstraint(name: 'uniq_author_reward', columns: ['author_id', 'reward_id'])]
class AuthorReward
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Author::class, inversedBy: 'authorRewards')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Author $author = null;

    #[ORM\ManyToOne(targetEntity: Reward::class, inversedBy: 'authorRewards')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Reward $reward = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    /**
     * Returns the unique identifier.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Returns the author owning this reward.
     */
    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    /**
     * Sets the author owning this reward.
     */
    public function setAuthor(?Author $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Returns the reward.
     */
    public function getReward(): ?Reward
    {
        return $this->reward;
    }

    /**
     * Sets the reward.
     */
    public function setReward(?Reward $reward): self
    {
        $this->reward = $reward;

        return $this;
    }

    /**
     * Returns the creation date of the association.
     */
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
