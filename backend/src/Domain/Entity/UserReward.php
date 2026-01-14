<?php

namespace App\Domain\Entity;

use App\Infrastructure\Repository\UserRewardRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents the association between a User and a Reward.
 */
#[ORM\Entity(repositoryClass: UserRewardRepository::class)]
#[ORM\Table(name: 'user_reward')]
#[ORM\UniqueConstraint(name: 'uniq_user_reward', columns: ['user_id', 'reward_id'])]
class UserReward
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'userRewards')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Reward::class, inversedBy: 'userRewards')]
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
     * Returns the user owning this reward.
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * Sets the user owning this reward.
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;

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
