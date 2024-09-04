<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Event\OrderStatusWasChanged;
use App\Message\NotifySubscriber;
use App\Repository\SubscriberRepository;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Messenger\MessageBusInterface;

final class NotifySubscribersListener
{
    public function __construct(
        private readonly SubscriberRepository $subscriberRepository,
        private readonly MessageBusInterface $messageBus,
    ) {
    }

    #[AsEventListener(event: OrderStatusWasChanged::class)]
    public function onOrderStatusWasChanged(OrderStatusWasChanged $event): void
    {
        // TODO: batch/chunks or expecting subscriber list quite small ?
        $subscribers = $this->subscriberRepository->getList($event->order->getStatus());

        foreach ($subscribers as $subscriber) {
            $message = new NotifySubscriber(
                orderId: $event->order->getId(),
                subscriberId: $subscriber->getId(),
                paymentProcessingId: $event->paymentProcessing->getId(),
                transactionId: $event->response->getTransactionId(),
                responseMessage: $event->response->getMessage(),
            );
            $this->messageBus->dispatch($message);
        }
    }
}
