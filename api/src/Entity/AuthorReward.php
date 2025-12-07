<?php

namespace App\Entity;

use App\Repository\AuthorRewardRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a reward earned by a specific author.
 */
#[ORM\Entity(repositoryClass: AuthorRewardRepository::class)]
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
    private \DateTimeImmutable $earnedAt;

    public function __construct()
    {
        $this->earnedAt = new \DateTimeImmutable();
    }

    /**
     * Returns the unique identifier of the author reward.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Returns the author who earned this reward.
     */
    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    /**
     * Sets the author who earned this reward.
     */
    public function setAuthor(?Author $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Returns the reward that was earned.
     */
    public function getReward(): ?Reward
    {
        return $this->reward;
    }

    /**
     * Sets the reward that was earned.
     */
    public function setReward(?Reward $reward): self
    {
        $this->reward = $reward;

        return $this;
    }

    /**
     * Returns the date when the reward was earned.
     */
    public function getEarnedAt(): \DateTimeImmutable
    {
        return $this->earnedAt;
    }

    /**
     * Sets the date when the reward was earned.
     */
    public function setEarnedAt(\DateTimeImmutable $earnedAt): self
    {
        $this->earnedAt = $earnedAt;

        return $this;
    }
}
