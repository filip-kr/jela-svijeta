<?php

namespace App\Entity;

use App\Repository\DishIngredientRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'dish_ingredient')]
#[ORM\Entity(repositoryClass: DishIngredientRepository::class)]
class DishIngredient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $dishId = null;

    #[ORM\Column]
    private ?int $ingredientId = null;

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

    public function getIngredientId(): ?int
    {
        return $this->ingredientId;
    }

    public function setIngredientId(int $ingredientId): self
    {
        $this->ingredientId = $ingredientId;

        return $this;
    }
}
