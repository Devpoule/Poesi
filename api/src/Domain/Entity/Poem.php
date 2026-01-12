<?php

namespace App\Domain\Entity;

use App\Domain\Enum\MoodColor;
use App\Domain\Enum\PoemStatus;
use App\Domain\Enum\SymbolType;
use App\Infrastructure\Repository\PoemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a poem written and possibly published by an author.
 */
#[ORM\Entity(repositoryClass: PoemRepository::class)]
class Poem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'poems')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\Column(enumType: PoemStatus::class)]
    private PoemStatus $status = PoemStatus::DRAFT;

    #[ORM\Column(enumType: MoodColor::class)]
    private MoodColor $moodColor;

    #[ORM\Column(enumType: SymbolType::class, nullable: true)]
    private ?SymbolType $symbolType = null;

    #[ORM\Column(length: 255)]
    private string $title;

    #[ORM\Column(type: 'text')]
    private string $content;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $publishedAt = null;

    /**
     * @var Collection<int, FeatherVote>
     */
    #[ORM\OneToMany(mappedBy: 'poem', targetEntity: FeatherVote::class, cascade: ['remove'], orphanRemoval: true)]
    private Collection $featherVotes;

    public function __construct()
    {
        $this->createdAt    = new \DateTimeImmutable();
        $this->moodColor    = MoodColor::BLUE;
        $this->featherVotes = new ArrayCollection();
    }

    /**
     * Returns the unique identifier of the poem.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Returns the author of the poem.
     */
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * Sets the author of the poem.
     */
    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Returns the publication status of the poem.
     */
    public function getStatus(): PoemStatus
    {
        return $this->status;
    }

    /**
     * Sets the publication status of the poem.
     */
    public function setStatus(PoemStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Returns the mood color associated with the poem.
     */
    public function getMoodColor(): MoodColor
    {
        return $this->moodColor;
    }

    /**
     * Sets the mood color associated with the poem.
     */
    public function setMoodColor(MoodColor $moodColor): self
    {
        $this->moodColor = $moodColor;

        return $this;
    }

    /**
     * Returns the current visual symbol of the poem (may evolve).
     */
    public function getSymbolType(): ?SymbolType
    {
        return $this->symbolType;
    }

    /**
     * Sets the current visual symbol of the poem (may evolve).
     */
    public function setSymbolType(?SymbolType $symbolType): self
    {
        $this->symbolType = $symbolType;

        return $this;
    }

    /**
     * Returns the poem title.
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Sets the poem title.
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Returns the content of the poem.
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Sets the content of the poem.
     */
    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Returns the creation date of the poem.
     */
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Returns the publication date of the poem.
     */
    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    /**
     * Sets the publication date of the poem.
     */
    public function setPublishedAt(?\DateTimeImmutable $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * Returns feather votes associated with the poem.
     *
     * @return Collection<int, FeatherVote>
     */
    public function getFeatherVotes(): Collection
    {
        return $this->featherVotes;
    }

    /**
     * Adds a feather vote to the poem.
     */
    public function addFeatherVote(FeatherVote $vote): self
    {
        if (!$this->featherVotes->contains($vote)) {
            $this->featherVotes->add($vote);
            $vote->setPoem($this);
        }

        return $this;
    }

    /**
     * Removes a feather vote from the poem.
     */
    public function removeFeatherVote(FeatherVote $vote): self
    {
        if ($this->featherVotes->removeElement($vote)) {
            if ($vote->getPoem() === $this) {
                $vote->setPoem(null);
            }
        }

        return $this;
    }
}
