<?php

namespace App\Entity;

use App\Repository\RewardRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a reward type that can be granted to authors.
 */
#[ORM\Entity(repositoryClass: RewardRepository::class)]
class Reward
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, unique: true)]
    private string $code;

    #[ORM\Column(length: 255)]
    private string $label;

    /**
     * @var Collection<int, AuthorReward>
     */
    #[ORM\OneToMany(mappedBy: 'reward', targetEntity: AuthorReward::class)]
    private Collection $authorRewards;

    public function __construct()
    {
        $this->authorRewards = new ArrayCollection();
    }

    /**
     * Returns the unique identifier of the reward.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Returns the technical code of the reward.
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * Sets the technical code of the reward.
     */
    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Returns the label of the reward.
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Sets the label of the reward.
     */
    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Returns all author-reward associations for this reward.
     *
     * @return Collection<int, AuthorReward>
     */
    public function getAuthorRewards(): Collection
    {
        return $this->authorRewards;
    }

    /**
     * Adds an author-reward association for this reward.
     */
    public function addAuthorReward(AuthorReward $authorReward): self
    {
        if (!$this->authorRewards->contains($authorReward)) {
            $this->authorRewards->add($authorReward);
            $authorReward->setReward($this);
        }

        return $this;
    }

    /**
     * Removes an author-reward association from this reward.
     */
    public function removeAuthorReward(AuthorReward $authorReward): self
    {
        if ($this->authorRewards->removeElement($authorReward)) {
            if ($authorReward->getReward() === $this) {
                $authorReward->setReward(null);
            }
        }

        return $this;
    }
}
