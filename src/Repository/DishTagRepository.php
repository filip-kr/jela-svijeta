<?php

namespace App\Repository;

use App\Entity\DishTag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DishTag>
 *
 * @method DishTag|null find($id, $lockMode = null, $lockVersion = null)
 * @method DishTag|null findOneBy(array $criteria, array $orderBy = null)
 * @method DishTag[]    findAll()
 * @method DishTag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DishTagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DishTag::class);
    }

    public function save(DishTag $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DishTag $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
