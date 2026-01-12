<?php

namespace App\Domain\Entity;

use App\Domain\Enum\FeatherType;
use App\Infrastructure\Repository\FeatherVoteRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a feather vote cast by a user on a poem.
 *
 * Recommended rule enforced at service level:
 * - One vote per (voter, poem). If a new vote is cast again, we update the existing one.
 */
#[ORM\Entity(repositoryClass: FeatherVoteRepository::class)]
#[ORM\Table(name: 'feather_vote')]
#[ORM\UniqueConstraint(name: 'uniq_voter_poem', columns: ['voter_id', 'poem_id'])]
class FeatherVote
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'featherVotes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $voter = null;

    #[ORM\ManyToOne(targetEntity: Poem::class, inversedBy: 'featherVotes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Poem $poem = null;

    #[ORM\Column(enumType: FeatherType::class)]
    private FeatherType $featherType;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $now = new \DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
        $this->featherType = FeatherType::BRONZE;
    }

    /**
     * Returns the unique identifier.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Returns the voter (user).
     */
    public function getVoter(): ?User
    {
        return $this->voter;
    }

    /**
     * Sets the voter (user).
     */
    public function setVoter(?User $voter): self
    {
        $this->voter = $voter;

        return $this;
    }

    /**
     * Returns the voted poem.
     */
    public function getPoem(): ?Poem
    {
        return $this->poem;
    }

    /**
     * Sets the voted poem.
     */
    public function setPoem(?Poem $poem): self
    {
        $this->poem = $poem;

        return $this;
    }

    /**
     * Returns the feather type.
     */
    public function getFeatherType(): FeatherType
    {
        return $this->featherType;
    }

    /**
     * Sets the feather type.
     *
     * Updating the feather type updates the updatedAt timestamp.
     */
    public function setFeatherType(FeatherType $featherType): self
    {
        $this->featherType = $featherType;
        $this->touch();

        return $this;
    }

    /**
     * Returns the creation date.
     */
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Returns the last update date.
     */
    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Updates updatedAt timestamp.
     */
    public function touch(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}
