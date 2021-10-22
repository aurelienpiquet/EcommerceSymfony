<?php

namespace App\Entity;

use App\Repository\CreatorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CreatorRepository::class)
 */
class Creator
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity=NewCollection::class, mappedBy="creator")
     */
    private $newCollections;

    /**
     * @ORM\OneToMany(targetEntity=Article::class, mappedBy="creator", orphanRemoval=true)
     */
    private $articles;

    public function __construct()
    {
        $this->newCollections = new ArrayCollection();
        $this->articles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function __toString(): ?string
    {
        return $this->getName();
    }

    /**
     * @return Collection|NewCollection[]
     */
    public function getNewCollections(): Collection
    {
        return $this->newCollections;
    }

    public function addNewCollection(NewCollection $newCollection): self
    {
        if (!$this->newCollections->contains($newCollection)) {
            $this->newCollections[] = $newCollection;
            $newCollection->setCreator($this);
        }

        return $this;
    }

    public function removeNewCollection(NewCollection $newCollection): self
    {
        if ($this->newCollections->removeElement($newCollection)) {
            // set the owning side to null (unless already changed)
            if ($newCollection->getCreator() === $this) {
                $newCollection->setCreator(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Article[]
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
            $article->setCreator($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->articles->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getCreator() === $this) {
                $article->setCreator(null);
            }
        }

        return $this;
    }
}
