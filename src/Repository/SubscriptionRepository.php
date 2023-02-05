<?php

namespace App\Repository;

use App\Entity\Subscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Subscription|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subscription|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subscription[]    findAll()
 * @method Subscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subscription::class);
    }

    // /**
    //  * @return Subscription[] Returns an array of Subscription objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Subscription
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findByGroupAndUser($user, $group){
        $qb = $this->createQueryBuilder('s');

        $qb
            ->innerJoin('s.groupe', 'groupe', Join::WITH, 'groupe.id = :idGroup')
            ->innerJoin('groupe.owner', 'owner', Join::WITH, 'owner.id = :user')
            ->where('s.group_statut = :statut1 or s.group_statut = :statut2')
            ->setParameters(['idGroup' => $group, 'user' => $user, 'statut1' => 'SEND_INVITATION', 'statut2'=> 'WAITING_ACCEPTANCE'])
        ;

        return $qb->getQuery()->getResult();
    }
    public function findByGroupAndUserCountSubscriber($user, $group){
        $qb = $this->createQueryBuilder('s');

        $qb
            ->select($qb->expr()->countDistinct('s.id'))
            ->innerJoin('s.groupe', 'groupe', Join::WITH, 'groupe.id = :idGroup')
            ->innerJoin('groupe.owner', 'owner', Join::WITH, 'owner.id = :user')
            ->where('s.group_statut = :statut1')
            ->andWhere('s.notification_checked = false')
            ->setParameters(['idGroup' => $group, 'user' => $user, 'statut1' => 'SEND_INVITATION'])
        ;

        return $qb->getQuery()->getSingleResult();
    }
}
