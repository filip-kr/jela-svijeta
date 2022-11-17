<?php

namespace App\Entity;

use App\Repository\DishTagRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'dish_tag')]
#[ORM\Entity(repositoryClass: DishTagRepository::class)]
class DishTag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $dishId = null;

    #[ORM\Column]
    private ?int $tagId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDishId(): ?int
    {
        return $this->dishId;
    }

    public function setDishId(int $dishId): self
    {
        $this->dishId = $dishId;

        return $this;
    }

    public function getTagId(): ?int
    {
        return $this->tagId;
    }

    public function setTagId(int $tagId): self
    {
        $this->tagId = $tagId;

        return $this;
    }
}
