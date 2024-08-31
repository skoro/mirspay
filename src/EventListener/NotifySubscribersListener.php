<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Event\OrderStatusWasChanged;
use App\Repository\SubscriberRepository;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

final class NotifySubscribersListener
{
    public function __construct(
        private readonly SubscriberRepository $subscriberRepository,
    ) {
    }

    #[AsEventListener(event: OrderStatusWasChanged::class)]
    public function onOrderStatusWasChanged(OrderStatusWasChanged $event): void
    {
        $subscribers = $this->subscriberRepository->getList($event->order->getStatus());
    }
}
