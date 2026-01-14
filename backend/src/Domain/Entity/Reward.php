<?php

namespace App\Domain\Entity;

use App\Infrastructure\Repository\RewardRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a reward that can be earned by users.
 *
 * Rewards are immutable catalog items (code + label),
 * and are linked to users via UserReward.
 */
#[ORM\Entity(repositoryClass: RewardRepository::class)]
#[ORM\Table(name: 'reward')]
class Reward
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Stable technical code (used by frontend & business rules).
     */
    #[ORM\Column(length: 50, unique: true)]
    private string $code;

    /**
     * Human-readable label.
     */
    #[ORM\Column(length: 255)]
    private string $label;

    /**
     * @var Collection<int, UserReward>
     */
    #[ORM\OneToMany(mappedBy: 'reward', targetEntity: UserReward::class)]
    private Collection $userRewards;

    public function __construct()
    {
        $this->userRewards = new ArrayCollection();
    }

    /**
     * Returns the reward identifier.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Returns the reward technical code.
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * Sets the reward technical code.
     */
    public function setCode(string $code): self
    {
        $this->code = strtoupper($code);

        return $this;
    }

    /**
     * Returns the reward label.
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Sets the reward label.
     */
    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Returns all user-reward associations.
     *
     * @return Collection<int, UserReward>
     */
    public function getUserRewards(): Collection
    {
        return $this->userRewards;
    }
}
