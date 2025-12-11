<?php

namespace App\Domain\Entity;

use App\Domain\Enum\FeatherType;
use App\Infrastructure\Repository\FeatherVoteRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a feather vote given by an author to a poem.
 */
#[ORM\Entity(repositoryClass: FeatherVoteRepository::class)]
class FeatherVote
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Poem::class, inversedBy: 'featherVotes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Poem $poem = null;

    #[ORM\ManyToOne(targetEntity: Author::class, inversedBy: 'featherVotes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Author $voter = null;

    #[ORM\Column(enumType: FeatherType::class)]
    private FeatherType $featherType;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt   = new \DateTimeImmutable();
        $this->featherType = FeatherType::BRONZE;
    }

    /**
     * Returns the unique identifier of the feather vote.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Returns the poem that received this feather vote.
     */
    public function getPoem(): ?Poem
    {
        return $this->poem;
    }

    /**
     * Sets the poem that received this feather vote.
     */
    public function setPoem(?Poem $poem): self
    {
        $this->poem = $poem;

        return $this;
    }

    /**
     * Returns the voter who cast this feather vote.
     */
    public function getVoter(): ?Author
    {
        return $this->voter;
    }

    /**
     * Sets the voter who cast this feather vote.
     */
    public function setVoter(?Author $voter): self
    {
        $this->voter = $voter;

        return $this;
    }

    /**
     * Returns the feather type of this vote.
     */
    public function getFeatherType(): FeatherType
    {
        return $this->featherType;
    }

    /**
     * Sets the feather type of this vote.
     */
    public function setFeatherType(FeatherType $featherType): self
    {
        $this->featherType = $featherType;

        return $this;
    }

    /**
     * Returns the creation date of this vote.
     */
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
