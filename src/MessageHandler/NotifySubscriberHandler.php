<?php

namespace App\MessageHandler;

use App\Message\NotifySubscriber;
use App\Repository\OrderRepository;
use App\Repository\SubscriberRepository;
use App\Subscriber\Action\SendSubscriberNotificationAction;
use App\Subscriber\Exception\ChannelMessageNotRegistered;
use App\Subscriber\Exception\NotificationChannelNotRegisteredException;
use Doctrine\ORM\EntityNotFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class NotifySubscriberHandler
{
    public function __construct(
        private SendSubscriberNotificationAction $sendSubscriberNotificationAction,
        private OrderRepository                  $orderRepository,
        private SubscriberRepository             $subscriberRepository,
        private LoggerInterface                  $logger,
    ) {
    }

    public function __invoke(NotifySubscriber $message): void
    {
        try {
            $order = $this->orderRepository->find($message->orderId);
            if (! $order) {
                throw new EntityNotFoundException("Order \"{$message->orderId}\" not found.");
            }

            $subscriber = $this->subscriberRepository->find($message->subscriberId);
            if (! $subscriber) {
                throw new EntityNotFoundException("Subscriber \"{$message->subscriberId}\" not found.");
            }

            // TODO: check the order status and subscriber's expected order status.

            $this->sendSubscriberNotificationAction->sendNotification($order, $subscriber, $message->response);
        } catch (EntityNotFoundException|ChannelMessageNotRegistered|NotificationChannelNotRegisteredException $e) {
            $this->logger->error($e->getMessage());
            return;
        }
    }
}
