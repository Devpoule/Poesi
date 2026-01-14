<?php

namespace App\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'user_relic')]
#[ORM\UniqueConstraint(name: 'uniq_user_relic_key', columns: ['user_id', 'relic_key'])]
class UserRelic
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'relics')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    /**
     * Stable identifier (matches resources/lore/relics.initial.json "key").
     */
    #[ORM\Column(name: 'relic_key', type: 'string', length: 64)]
    private string $relicKey;

    #[ORM\Column(name: 'granted_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $grantedAt;

    /**
     * Optional human explanation for the award.
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $reason = null;

    /**
     * Optional structured context (poem id, thresholds, counters...).
     *
     * @var array<string, mixed>|null
     */
    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $context = null;

    /**
     * @param array<string, mixed>|null $context
     */
    public function __construct(
        User $user,
        string $relicKey,
        ?string $reason = null,
        ?array $context = null
    ) {
        $this->relicKey  = $relicKey;
        $this->grantedAt = new \DateTimeImmutable();
        $this->reason    = $reason;
        $this->context   = $context;

        $this->setUser($user);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getRelicKey(): string
    {
        return $this->relicKey;
    }

    public function getGrantedAt(): \DateTimeImmutable
    {
        return $this->grantedAt;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getContext(): ?array
    {
        return $this->context;
    }
}
