<?php

namespace App\Repository;

use App\Entity\UserRecovery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserRecovery|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserRecovery|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserRecovery[]    findAll()
 * @method UserRecovery[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRecoveryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserRecovery::class);
    }

    // /**
    //  * @return UserRecovery[] Returns an array of UserRecovery objects
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
    public function findOneBySomeField($value): ?UserRecovery
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
