<?php

namespace App\Repository;

use App\Entity\WinnedCombination;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method WinnedCombination|null find($id, $lockMode = null, $lockVersion = null)
 * @method WinnedCombination|null findOneBy(array $criteria, array $orderBy = null)
 * @method WinnedCombination[]    findAll()
 * @method WinnedCombination[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WinnedCombinationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WinnedCombination::class);
    }

    // /**
    //  * @return WinnedCombination[] Returns an array of WinnedCombination entities
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?WinnedCombination
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
