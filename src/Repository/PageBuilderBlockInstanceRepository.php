<?php

namespace JulianKoster\PageBuilderBundle\Repository;

use JulianKoster\PageBuilderBundle\Entity\PageBuilderBlockInstance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PageBuilderBlockInstance>
 */
class PageBuilderBlockInstanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PageBuilderBlockInstance::class);
    }

    //    /**
    //     * @return PageBuilderBlockInstance[] Returns an array of PageBuilderBlockInstance objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

//        public function findOneBySomeField($value): ?PageBuilderBlockInstance
//        {
//            return $this->createQueryBuilder('p')
//                ->andWhere('p.exampleField = :val')
//                ->setParameter('val', $value)
//                ->getQuery()
//                ->getOneOrNullResult()
//            ;
//        }
}
