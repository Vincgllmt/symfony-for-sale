<?php

namespace App\Repository;

use App\Entity\Advertisement;
use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Advertisement>
 *
 * @method Advertisement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Advertisement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Advertisement[]    findAll()
 * @method Advertisement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdvertisementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Advertisement::class);
    }

    /**Optimised function with a left join for grab all the categories in one request
     * @return Query
     */
    public function queryAllByDate(): Query
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.category', 'ca')
            ->addSelect('ca')
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
        ;
    }

    /**
     * Find all advertisement for a category.
     *
     * @param Category $category The category
     *
     * @return QueryBuilder All advertisement
     */
    public function queryByCategory(Category $category): QueryBuilder
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.category', 'ca')
            ->addSelect('ca')
            ->where('ca.id = :id')
            ->orderBy('a.createdAt', 'DESC')
            ->setParameter('id', $category->getId());
    }
    //    /**
    //     * @return Advertisement[] Returns an array of Advertisement objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Advertisement
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
