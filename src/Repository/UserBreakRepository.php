<?php

namespace App\Repository;

use App\Entity\UserBreak;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserBreak|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserBreak|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserBreak[]    findAll()
 * @method UserBreak[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserBreakRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserBreak::class);
    }

    // /**
    //  * @return UserBreak[] Returns an array of UserBreak objects
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
    public function findOneBySomeField($value): ?UserBreak
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
