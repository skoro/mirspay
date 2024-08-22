<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\OrderStatus;
use App\Entity\PaymentProcessing;
use App\Message\PurchaseOrder;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class PaymentPendingHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private OrderRepository $orderRepository,
    ) {
    }

    public function __invoke(PurchaseOrder $message): void
    {
        $order = $this->orderRepository->find($message->orderId);
        if (! $order) {
            // TODO: final exception, no need to restart the job
            throw new EntityNotFoundException();
        }

        $purchaseResponse = $message->redirectResponse;

        $processing = PaymentProcessing::create(
            order: $order,
            handler: $this,
            message: $message,
            request: $purchaseResponse->getRequest(),
            response: $purchaseResponse,
        );
        $this->entityManager->persist($processing);

        $order->setStatus(OrderStatus::PAYMENT_PENDING);

        $this->entityManager->flush();
    }
}
