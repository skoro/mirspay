<?php

namespace Mirspay\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Mirspay\Entity\OrderStatus;
use Mirspay\Entity\Subscriber;

/**
 * @extends ServiceEntityRepository<Subscriber>
 */
class SubscriberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subscriber::class);
    }

    public function hasSubscriber(string $hash): bool
    {
        $result = $this->createQueryBuilder('s')
            ->where('s.hash = :hash')
            ->setParameter('hash', $hash)
            ->select('1')
            ->getQuery()
            ->getOneOrNullResult();

        return (bool) $result;
    }

    /**
     * @param OrderStatus|null $orderStatus Filter by order status.
     * @return Subscriber[]
     */
    public function getList(?OrderStatus $orderStatus = null): array
    {
        $query = $this->createQueryBuilder('s');
        if ($orderStatus) {
            $query->where('s.orderStatus = :orderStatus');
            $query->setParameter('orderStatus', $orderStatus);
        }

        return $query->getQuery()->getResult();
    }
}
