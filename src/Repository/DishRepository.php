<?php

namespace App\Repository;

use App\Entity\Dish;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Dish>
 *
 * @method Dish|null find($id, $lockMode = null, $lockVersion = null)
 * @method Dish|null findOneBy(array $criteria, array $orderBy = null)
 * @method Dish[]    findAll()
 * @method Dish[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DishRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dish::class);
    }

    public function save(Dish $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Dish $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByParameters(array $params): array
    {
        $query = $this->createQueryBuilder('d')
            ->select('d');

        if (isset($params['diff_time'])) {
            $date = date('Y-m-d H:i:s', $params['diff_time']);
            $query->andWhere('d.createdAt > :date')
                ->orWhere('d.updatedAt > :date')
                ->orWhere('d.deletedAt > :date')
                ->setParameter('date', $date);
        } else {
            $query->andWhere('d.status = \'created\'');
        }

        if (isset($params['category'])) {
            if ($params['category'] == 'NULL') {
                $query->andWhere('d.category IS NULL');
            } elseif ($params['category'] == '!NULL') {
                $query->andWhere('d.category IS NOT NULL');
            } else {
                $query->andWhere('d.category = :category')
                    ->setParameter('category', $params['category']);
            }
        }

        if (isset($params['tags'])) {
            $tags = explode(',', $params['tags']);
            $query->innerJoin('App\Entity\DishTag', 'dt', 'WITH', 'dt.dishId = d.id')
                ->andWhere('dt.tagId IN (:tags)')
                ->setParameter('tags', $tags);
        }

        // dd($query->getQuery());
        return $query->getQuery()
            ->getResult();
    }
}
