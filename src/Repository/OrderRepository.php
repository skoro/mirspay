<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function findByExternalOrderIdAndPaymentGateway(string $externalOrderId, string $paymentGateway): ?Order
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.externalOrderId = :externalOrderId')
            ->andWhere('o.paymentGateway = :paymentGateway')
            ->setParameter('externalOrderId', $externalOrderId)
            ->setParameter('paymentGateway', $paymentGateway)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
