<?php

namespace App\Repository;

use App\Entity\Place;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Place|null find($id, $lockMode = null, $lockVersion = null)
 * @method Place|null findOneBy(array $criteria, array $orderBy = null)
 * @method Place[]    findAll()
 * @method Place[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlaceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Place::class);
    }


    public function findPlacesForMapOnListGroup()
    {
        $qb = $this->createQueryBuilder('p');

        $qb->select('p.Adress', 'p.id', 'p.lat', 'p.lng')
            ->innerJoin('p.homeGroup','groupe')->addSelect('groupe.id as groupId', 'groupe.title', 'groupe.theme', 'groupe.description', 'groupe.location', 'groupe.frequence')
        ;

        return $qb->getQuery()->getResult();
    }
    public function findPlacesForMapOnListPrayer()
    {
        $qb = $this->createQueryBuilder('p');

        $qb->select('p.Adress', 'p.id', 'p.lat', 'p.lng')
            ->innerJoin('p.prayers','prayers')->addSelect('prayers.id as prayersId',  'prayers.description')
        ;

        return $qb->getQuery()->getResult();
    }
    // /**
    //  * @return Place[] Returns an array of Place objects
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
    public function findOneBySomeField($value): ?Place
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
