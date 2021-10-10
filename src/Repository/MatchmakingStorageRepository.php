<?php

namespace App\Repository;

use App\Entity\MatchmakingStorage;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MatchmakingStorage|null find($id, $lockMode = null, $lockVersion = null)
 * @method MatchmakingStorage|null findOneBy(array $criteria, array $orderBy = null)
 * @method MatchmakingStorage[]    findAll()
 * @method MatchmakingStorage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MatchmakingStorageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MatchmakingStorage::class);
    }

    /**
     * @param User $user
     * @return array
     */
    public function findActiveMatchmaking(User $user): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.user != :userId')
            ->andWhere('s.createdAt > :now')
            ->setParameters([
                'userId' => $user->getId(),
                'now' => (new \DateTime())->modify('-30 seconds')
            ])
            ->getQuery()
            ->getResult();
    }

    public function findActiveMatchmakingForUserByKindOfGame(User $user, int $kindOfGame): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.user != :userId')
            ->andWhere('s.kindOfGame = :kindOfGame')
            ->andWhere('s.createdAt > :now')
            ->setParameters([
                'userId' => $user->getId(),
                'kindOfGame' => $kindOfGame,
                'now' => (new \DateTime())->modify('-30 seconds')
            ])
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array
     */
    public function findAllOldRecords(): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.updatedAt < :time')
            ->setParameter('time', (new \DateTime())->modify('-30 seconds'))
            ->getQuery()
            ->getResult();
    }

    /**
     * @param User $user
     * @return MatchmakingStorage|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneMatchmakingByUser(User $user): ?MatchmakingStorage
    {
        return $this->createQueryBuilder('s')
            ->where('s.user = :userId')
            ->setParameter('userId', $user->getId())
            ->getQuery()
            ->getOneOrNullResult();
    }

    // /**
    //  * @return MatchmakingStorage[] Returns an array of MatchmakingStorage objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MatchmakingStorage
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
