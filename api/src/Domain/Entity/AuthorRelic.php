<?php

namespace App\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'author_relic')]
#[ORM\UniqueConstraint(name: 'uniq_author_relic_key', columns: ['author_id', 'relic_key'])]
class AuthorRelic
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Author::class, inversedBy: 'relics')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Author $author = null;

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
        Author $author,
        string $relicKey,
        ?string $reason = null,
        ?array $context = null
    ) {
        $this->relicKey  = $relicKey;
        $this->grantedAt = new \DateTimeImmutable();
        $this->reason    = $reason;
        $this->context   = $context;

        $this->setAuthor($author);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    public function setAuthor(?Author $author): self
    {
        $this->author = $author;

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
