<?php

namespace App\Repository;

use App\Entity\PaymentProcessing;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PaymentProcessing>
 */
class PaymentProcessingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaymentProcessing::class);
    }

    /**
     * @return PaymentProcessing[]
     */
    public function findByOrderId(int $orderId): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.orderId = :orderId')
            ->setParameter('orderId', $orderId)
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return PaymentProcessing[] Returns an array of PaymentProcessing objects
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

    //    public function findOneBySomeField($value): ?PaymentProcessing
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
