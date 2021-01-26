<?php

namespace App\Repository;

use App\Entity\TimeParam;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TimeParam|null find($id, $lockMode = null, $lockVersion = null)
 * @method TimeParam|null findOneBy(array $criteria, array $orderBy = null)
 * @method TimeParam[]    findAll()
 * @method TimeParam[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TimeParamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TimeParam::class);
    }

    // /**
    //  * @return TimeParam[] Returns an array of TimeParam objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TimeParam
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
