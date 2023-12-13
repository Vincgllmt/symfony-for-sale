<?php

namespace App\Repository;

use App\Entity\Advertisement;
use App\Entity\Category;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
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
    public function queryAllByDate(string $search = null): Query
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.category', 'ca')
            ->addSelect('ca')
            ->orderBy('a.createdAt', 'DESC');
        if ($search) {
            $qb->where('lower(a.title) LIKE lower(:search)')
                ->orWhere('lower(a.description) LIKE lower(:search)')
                ->setParameter('search', '%'.$search.'%');
        }

        return $qb->getQuery();
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

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function findOneWithCategory(int $id)
    {
        return $this->createQueryBuilder('a')
            ->where('a.id = :id')
            ->leftJoin('a.category', 'c')
            ->addSelect('c')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleResult();
    }

    public function queryByUser($userId): Query
    {
        return $this->createQueryBuilder('a')
            ->where('a.owner = :userId')
            ->setParameter('userId', $userId)
            ->getQuery();
    }

    public function findLikedByUser(User $user): Query
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.likes', 'l')
            ->addSelect('l')
            ->where('l.id = :id')
            ->setParameter('id', $user->getId())
            ->getQuery();
    }
}
