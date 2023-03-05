<?php

namespace App\Repository;

use App\Entity\Subscription;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function findMemberForGroup($group)
    {
        $qb = $this->createQueryBuilder('user');

        $qb->select('user.id','user.firstname', 'user.lastname', 'user.email')
            ->innerJoin('user.subscriptions', 'subscriptions')
            ->innerJoin('subscriptions.groupe', 'groupe', Join::WITH, 'groupe.id = :idGroup')
            ->addSelect('subscriptions.id as idSubscrib', 'subscriptions.group_acceptedAt as dateAccept', 'groupe.id as GroupId')
            ->where('subscriptions.group_statut = :statut')
            ->setParameters(['idGroup' => $group, 'statut' => Subscription::ACCEPTED])
        ;

        return $qb->getQuery()->getArrayResult();

    }
    public function findBlockedMemberForGroup($group)
    {
        $qb = $this->createQueryBuilder('user');

        $qb->select('user.id','user.firstname', 'user.lastname', 'user.email')
            ->innerJoin('user.subscriptions', 'subscriptions')
            ->innerJoin('subscriptions.groupe', 'groupe', Join::WITH, 'groupe.id = :idGroup')
            ->addSelect('subscriptions.id as idSubscrib', 'subscriptions.group_acceptedAt as dateAccept', 'groupe.id as GroupId')
            ->where('subscriptions.group_statut = :statut')
            ->setParameters(['idGroup' => $group, 'statut' => Subscription::BLOCKED])
        ;

        return $qb->getQuery()->getArrayResult();

    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
