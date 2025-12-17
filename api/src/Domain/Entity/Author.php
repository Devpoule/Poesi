<?php

namespace App\Domain\Entity;

use App\Domain\Enum\MoodColor;
use App\Infrastructure\Repository\AuthorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a poetic author using the application.
 */
#[ORM\Entity(repositoryClass: AuthorRepository::class)]
class Author
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private string $pseudo;

    #[ORM\Column(length: 180, unique: true)]
    private string $email;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\ManyToOne(targetEntity: Totem::class, inversedBy: 'authors')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Totem $totem = null;

    #[ORM\Column(enumType: MoodColor::class)]
    private MoodColor $moodColor;

    /**
     * @var Collection<int, Poem>
     */
    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Poem::class)]
    private Collection $poems;

    /**
     * @var Collection<int, FeatherVote>
     */
    #[ORM\OneToMany(mappedBy: 'voter', targetEntity: FeatherVote::class)]
    private Collection $featherVotes;

    /**
     * @var Collection<int, AuthorReward>
     */
    #[ORM\OneToMany(mappedBy: 'author', targetEntity: AuthorReward::class)]
    private Collection $authorRewards;

    /**
     * Relics are rare, non-votable rewards granted to the author (milestones, editorial picks, etc.).
     *
     * @var Collection<int, AuthorRelic>
     */
    #[ORM\OneToMany(mappedBy: 'author', targetEntity: AuthorRelic::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $relics;

    public function __construct()
    {
        $this->createdAt     = new \DateTimeImmutable();
        $this->moodColor     = MoodColor::BLUE;
        $this->poems         = new ArrayCollection();
        $this->featherVotes  = new ArrayCollection();
        $this->authorRewards = new ArrayCollection();
        $this->relics        = new ArrayCollection();
    }

    /**
     * Returns the unique identifier of the author.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Returns the author's pseudo.
     */
    public function getPseudo(): string
    {
        return $this->pseudo;
    }

    /**
     * Sets the author's pseudo.
     */
    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    /**
     * Returns the author's email.
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Sets the author's email.
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Returns the date the author was created.
     */
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Returns the totem associated with the author.
     */
    public function getTotem(): ?Totem
    {
        return $this->totem;
    }

    /**
     * Sets the totem associated with the author.
     */
    public function setTotem(?Totem $totem): self
    {
        $this->totem = $totem;

        return $this;
    }

    /**
     * Returns the current mood color of the author.
     */
    public function getMoodColor(): MoodColor
    {
        return $this->moodColor;
    }

    /**
     * Sets the current mood color of the author.
     */
    public function setMoodColor(MoodColor $moodColor): self
    {
        $this->moodColor = $moodColor;

        return $this;
    }

    /**
     * Returns poems written by the author.
     *
     * @return Collection<int, Poem>
     */
    public function getPoems(): Collection
    {
        return $this->poems;
    }

    /**
     * Adds a poem to the author's collection.
     */
    public function addPoem(Poem $poem): self
    {
        if (!$this->poems->contains($poem)) {
            $this->poems->add($poem);
            $poem->setAuthor($this);
        }

        return $this;
    }

    /**
     * Removes a poem from the author's collection.
     */
    public function removePoem(Poem $poem): self
    {
        if ($this->poems->removeElement($poem)) {
            if ($poem->getAuthor() === $this) {
                $poem->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * Returns feather votes cast by the author.
     *
     * @return Collection<int, FeatherVote>
     */
    public function getFeatherVotes(): Collection
    {
        return $this->featherVotes;
    }

    /**
     * Adds a feather vote to the author.
     */
    public function addFeatherVote(FeatherVote $vote): self
    {
        if (!$this->featherVotes->contains($vote)) {
            $this->featherVotes->add($vote);
            $vote->setVoter($this);
        }

        return $this;
    }

    /**
     * Removes a feather vote from the author.
     */
    public function removeFeatherVote(FeatherVote $vote): self
    {
        if ($this->featherVotes->removeElement($vote)) {
            if ($vote->getVoter() === $this) {
                $vote->setVoter(null);
            }
        }

        return $this;
    }

    /**
     * Returns rewards obtained by the author.
     *
     * @return Collection<int, AuthorReward>
     */
    public function getAuthorRewards(): Collection
    {
        return $this->authorRewards;
    }

    /**
     * Adds a reward to the author.
     */
    public function addAuthorReward(AuthorReward $authorReward): self
    {
        if (!$this->authorRewards->contains($authorReward)) {
            $this->authorRewards->add($authorReward);
            $authorReward->setAuthor($this);
        }

        return $this;
    }

    /**
     * Removes a reward from the author.
     */
    public function removeAuthorReward(AuthorReward $authorReward): self
    {
        if ($this->authorRewards->removeElement($authorReward)) {
            if ($authorReward->getAuthor() === $this) {
                $authorReward->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * Returns relics owned by the author.
     *
     * @return Collection<int, AuthorRelic>
     */
    public function getRelics(): Collection
    {
        return $this->relics;
    }

    /**
     * Grants a relic to the author.
     */
    public function addRelic(AuthorRelic $relic): self
    {
        if (!$this->relics->contains($relic)) {
            $this->relics->add($relic);
            $relic->setAuthor($this);
        }

        return $this;
    }

    /**
     * Removes a relic from the author.
     */
    public function removeRelic(AuthorRelic $relic): self
    {
        if ($this->relics->removeElement($relic)) {
            if ($relic->getAuthor() === $this) {
                $relic->setAuthor(null);
            }
        }

        return $this;
    }
}
