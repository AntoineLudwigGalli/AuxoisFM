<?php

namespace App\Repository;

use App\Entity\ShowWebpageOptions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ShowWebpageOptions>
 *
 * @method ShowWebpageOptions|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShowWebpageOptions|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShowWebpageOptions[]    findAll()
 * @method ShowWebpageOptions[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShowWebpageOptionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShowWebpageOptions::class);
    }

    public function add(ShowWebpageOptions $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ShowWebpageOptions $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ShowWebpageOptions[] Returns an array of ShowWebpageOptions objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ShowWebpageOptions
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
