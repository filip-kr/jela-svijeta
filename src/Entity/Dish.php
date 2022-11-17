<?php

namespace App\Entity;

use App\Repository\DishRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;

#[ORM\Table(name: 'dish')]
#[ORM\Entity(repositoryClass: DishRepository::class)]
class Dish implements Translatable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Gedmo\Translatable]
    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[Gedmo\Translatable]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column]
    private ?\DateTime $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $updatedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $deletedAt = null;

    #[ORM\ManyToOne(inversedBy: 'dishes')]
    private ?Category $category = null;

    // #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'dishes')]
    // private Collection $tags;

    // #[ORM\ManyToMany(targetEntity: Ingredient::class, inversedBy: 'dishes')]
    // private Collection $ingredients;

    #[Gedmo\Locale]
    private $locale;

    // public function __construct()
    // {
    //     $this->tags = new ArrayCollection();
    //     $this->ingredients = new ArrayCollection();
    // }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getDeletedAt(): ?\DateTime
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTime $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    // /**
    //  * @return Collection<int, Tag>
    //  */
    // public function getTags(): Collection
    // {
    //     return $this->tags;
    // }

    // public function addTag(Tag $tag): self
    // {
    //     if (!$this->tags->contains($tag)) {
    //         $this->tags->add($tag);
    //     }

    //     return $this;
    // }

    // public function removeTag(Tag $tag): self
    // {
    //     $this->tags->removeElement($tag);

    //     return $this;
    // }

    // /**
    //  * @return Collection<int, Ingredient>
    //  */
    // public function getIngredients(): Collection
    // {
    //     return $this->ingredients;
    // }

    // public function addIngredient(Ingredient $ingredient): self
    // {
    //     if (!$this->ingredients->contains($ingredient)) {
    //         $this->ingredients->add($ingredient);
    //     }

    //     return $this;
    // }

    // public function removeIngredient(Ingredient $ingredient): self
    // {
    //     $this->ingredients->removeElement($ingredient);

    //     return $this;
    // }

    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
    }
}
