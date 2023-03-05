<?php

namespace App\Repository;

use App\Entity\Prayer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Prayer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Prayer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Prayer[]    findAll()
 * @method Prayer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrayerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Prayer::class);
    }

    public function findForPaginationGroup($group, $member)
    {

        $qb = $this->createQueryBuilder('p');

        $qb
            ->innerJoin('p.groupe', 'groupe', Join::WITH, 'groupe.id = :group')
            ->setParameter(':group', $group);

        if (!$member){
            $qb->where('e.security = false');
        }

        return $qb->getQuery();

    }
    // /**
    //  * @return Prayer[] Returns an array of Prayer objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Prayer
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
