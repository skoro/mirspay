<?php

namespace Mirspay\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Mirspay\Entity\PaymentProcessing;

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
}
