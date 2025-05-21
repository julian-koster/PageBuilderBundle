<?php

namespace JulianKoster\PageBuilderBundle\Repository;

use JulianKoster\PageBuilderBundle\Entity\PageBuilderBlockInstance;
use JulianKoster\PageBuilderBundle\Entity\PageBuilderBlockOverrides;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PageBuilderBlockOverrides>
 */
class PageBuilderBlockOverridesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PageBuilderBlockOverrides::class);
    }

    public function getExistingOverridesToDelete(PageBuilderBlockInstance $blockInstance, string $type, string $baseKey, string $key)
    {
        return $this->createQueryBuilder('p')
            ->where('p.pageBuilderBlockInstance = :instance')
            ->andWhere('p.type = :type')
            ->andWhere('p.fieldKey LIKE :baseKeyPattern')
            ->andWhere('p.fieldKey != :currentKey')
            ->setParameters([
                'instance' => $blockInstance,
                'type' => $type,
                'baseKeyPattern' => $baseKey . '[%',
                'currentKey' => $key,
            ])
            ->getQuery()
            ->getResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getExistingOverridesByBaseKey(PageBuilderBlockInstance $blockInstance, string $type, string $baseKey)
    {
        return $this->createQueryBuilder('p')
            ->where('p.pageBuilderBlockInstance = :instance')
            ->andWhere('p.type = :type')
            ->andWhere('p.fieldKey LIKE :baseKeyPattern')
            ->setParameters([
                'instance' => $blockInstance,
                'type' => $type,
                'baseKeyPattern' => $baseKey . '[%',
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }

//    /**
//     * @return PageBuilderBlockOverrides[] Returns an array of PageBuilderBlockOverrides objects
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

//    public function findOneBySomeField($value): ?PageBuilderBlockOverrides
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
