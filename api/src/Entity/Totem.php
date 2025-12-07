<?php

namespace App\Entity;

use App\Repository\TotemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a bird totem that can be chosen by authors.
 */
#[ORM\Entity(repositoryClass: TotemRepository::class)]
class Totem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 120)]
    private string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $picture = null;

    /**
     * @var Collection<int, Author>
     */
    #[ORM\OneToMany(mappedBy: 'totem', targetEntity: Author::class)]
    private Collection $authors;

    public function __construct()
    {
        $this->authors = new ArrayCollection();
    }

    /**
     * Returns the unique identifier of the totem.
     */
    public function getId(): ?int
    {
        return $this->id;
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
     * Returns authors associated with this totem.
     *
     * @return Collection<int, Author>
     */
    public function getAuthors(): Collection
    {
        return $this->authors;
    }

    /**
     * Adds an author to this totem.
     */
    public function addAuthor(Author $author): self
    {
        if (!$this->authors->contains($author)) {
            $this->authors->add($author);
            $author->setTotem($this);
        }

        return $this;
    }

    /**
     * Removes an author from this totem.
     */
    public function removeAuthor(Author $author): self
    {
        if ($this->authors->removeElement($author)) {
            if ($author->getTotem() === $this) {
                $author->setTotem(null);
            }
        }

        return $this;
    }
}
