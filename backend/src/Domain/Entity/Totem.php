<?php

namespace App\Domain\Entity;

use App\Infrastructure\Repository\TotemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a bird totem that can be chosen by users.
 */
#[ORM\Entity(repositoryClass: TotemRepository::class)]
#[ORM\Table(name: 'totem')]
class Totem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: 'totem_key', length: 100, unique: true, nullable: false)]
    private string $key;

    #[ORM\Column(length: 120, nullable: false)]
    private string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $picture = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(mappedBy: 'totem', targetEntity: User::class)]
    private Collection $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    /**
     * Returns the unique identifier of the totem.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Returns the unique key of the totem.
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Sets the unique key of the totem.
     */
    public function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Returns the totem name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the totem name.
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Returns the description of the totem.
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Sets the description of the totem.
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Returns the picture path of the totem.
     */
    public function getPicture(): ?string
    {
        return $this->picture;
    }

    /**
     * Sets the picture path of the totem.
     */
    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Returns users associated with this totem.
     *
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    /**
     * Adds a user to this totem.
     */
    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setTotem($this);
        }

        return $this;
    }

    /**
     * Removes a user from this totem.
     */
    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            if ($user->getTotem() === $this) {
                $user->setTotem(null);
            }
        }

        return $this;
    }
}
