<?php

namespace App\Domain\Entity;

use App\Domain\Enum\MoodColor;
use App\Infrastructure\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private string $email;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $pseudo = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\ManyToOne(targetEntity: Totem::class, inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Totem $totem = null;

    #[ORM\Column(enumType: MoodColor::class)]
    private MoodColor $moodColor;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(type: 'string')]
    private string $password;

    #[ORM\Column(name: 'failed_login_attempts', type: 'integer')]
    private int $failedLoginAttempts = 0;

    #[ORM\Column(name: 'locked_at', type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $lockedAt = null;

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
     * @var Collection<int, UserReward>
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserReward::class)]
    private Collection $userRewards;

    /**
     * Relics are rare, non-votable rewards granted to the user (milestones, editorial picks, etc.).
     *
     * @var Collection<int, UserRelic>
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserRelic::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $relics;

    public function __construct()
    {
        $this->createdAt    = new \DateTimeImmutable();
        $this->moodColor    = MoodColor::BLUE;
        $this->poems        = new ArrayCollection();
        $this->featherVotes = new ArrayCollection();
        $this->userRewards  = new ArrayCollection();
        $this->relics       = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(?string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getTotem(): ?Totem
    {
        return $this->totem;
    }

    public function setTotem(?Totem $totem): self
    {
        $this->totem = $totem;

        return $this;
    }

    public function getMoodColor(): MoodColor
    {
        return $this->moodColor;
    }

    public function setMoodColor(MoodColor $moodColor): self
    {
        $this->moodColor = $moodColor;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_values(array_unique($roles));
    }

    /**
     * @param string[] $roles
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $hashedPassword): self
    {
        $this->password = $hashedPassword;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function eraseCredentials(): void
    {
    }

    public function getFailedLoginAttempts(): int
    {
        return $this->failedLoginAttempts;
    }

    public function incrementFailedLoginAttempts(): void
    {
        $this->failedLoginAttempts++;
    }

    public function resetFailedLoginAttempts(): void
    {
        $this->failedLoginAttempts = 0;
        $this->lockedAt = null;
    }

    public function lock(): void
    {
        $this->lockedAt = new \DateTimeImmutable();
    }

    public function isLocked(): bool
    {
        return $this->lockedAt !== null;
    }

    /**
     * @return Collection<int, Poem>
     */
    public function getPoems(): Collection
    {
        return $this->poems;
    }

    public function addPoem(Poem $poem): self
    {
        if (!$this->poems->contains($poem)) {
            $this->poems->add($poem);
            $poem->setAuthor($this);
        }

        return $this;
    }

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
     * @return Collection<int, FeatherVote>
     */
    public function getFeatherVotes(): Collection
    {
        return $this->featherVotes;
    }

    public function addFeatherVote(FeatherVote $vote): self
    {
        if (!$this->featherVotes->contains($vote)) {
            $this->featherVotes->add($vote);
            $vote->setVoter($this);
        }

        return $this;
    }

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
     * @return Collection<int, UserReward>
     */
    public function getUserRewards(): Collection
    {
        return $this->userRewards;
    }

    public function addUserReward(UserReward $userReward): self
    {
        if (!$this->userRewards->contains($userReward)) {
            $this->userRewards->add($userReward);
            $userReward->setUser($this);
        }

        return $this;
    }

    public function removeUserReward(UserReward $userReward): self
    {
        if ($this->userRewards->removeElement($userReward)) {
            if ($userReward->getUser() === $this) {
                $userReward->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserRelic>
     */
    public function getRelics(): Collection
    {
        return $this->relics;
    }

    public function addRelic(UserRelic $relic): self
    {
        if (!$this->relics->contains($relic)) {
            $this->relics->add($relic);
            $relic->setUser($this);
        }

        return $this;
    }

    public function removeRelic(UserRelic $relic): self
    {
        if ($this->relics->removeElement($relic)) {
            if ($relic->getUser() === $this) {
                $relic->setUser(null);
            }
        }

        return $this;
    }
}
