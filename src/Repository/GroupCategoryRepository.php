<?php

namespace App\Repository;

use App\Entity\GroupCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GroupCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method GroupCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method GroupCategory[]    findAll()
 * @method GroupCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GroupCategory::class);
    }

    // /**
    //  * @return GroupCategory[] Returns an array of GroupCategory objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GroupCategory
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
