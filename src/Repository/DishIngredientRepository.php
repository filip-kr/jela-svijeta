<?php

namespace App\Repository;

use App\Entity\DishIngredient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DishIngredient>
 *
 * @method DishIngredient|null find($id, $lockMode = null, $lockVersion = null)
 * @method DishIngredient|null findOneBy(array $criteria, array $orderBy = null)
 * @method DishIngredient[]    findAll()
 * @method DishIngredient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DishIngredientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DishIngredient::class);
    }

    public function save(DishIngredient $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DishIngredient $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
