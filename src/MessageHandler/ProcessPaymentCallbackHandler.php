<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\OrderStatus;
use App\Entity\PaymentProcessing;
use App\Message\ProcessPaymentCallback;
use App\Payment\PaymentGatewayRegistry;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ProcessPaymentCallbackHandler
{
    public function __construct(
        private OrderRepository $orderRepository,
        private PaymentGatewayRegistry $paymentGatewayRegistry,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(ProcessPaymentCallback $message): void
    {
        $order = $this->orderRepository->find($message->orderId);
        if (! $order) {
            // TODO: do not restart message
            throw new EntityNotFoundException("Order with id '{$message->orderId}' not found.");
        }

        $paymentGateway = $this->paymentGatewayRegistry->getGatewayById($order->getPaymentGateway());

        $response = $paymentGateway->getServerCallbackHandler()->handleCallback($message->content, $message->headers);

        if ($response->isSuccessful()) {
            $order->setStatus(OrderStatus::PAYMENT_RECEIVED);
        } else {
            $order->setStatus(OrderStatus::PAYMENT_FAILED);
        }

        $paymentProcessing = PaymentProcessing::create(
            order: $order,
            handler: $this,
            message: $message,
            request: $response->getRequest(),
            response: $response,
        );

        $this->entityManager->persist($order);
        $this->entityManager->persist($paymentProcessing);

        $this->entityManager->flush();
    }
}
