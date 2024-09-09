<?php

namespace Mirspay\MessageHandler;

use Doctrine\ORM\EntityNotFoundException;
use Mirspay\Message\NotifySubscriber;
use Mirspay\Repository\PaymentProcessingRepository;
use Mirspay\Repository\SubscriberRepository;
use Mirspay\Subscriber\Action\SendSubscriberNotificationAction;
use Mirspay\Subscriber\Exception\ChannelMessageNotRegistered;
use Mirspay\Subscriber\Exception\NotificationChannelNotRegisteredException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class NotifySubscriberHandler
{
    public function __construct(
        private SendSubscriberNotificationAction $sendSubscriberNotificationAction,
        private SubscriberRepository             $subscriberRepository,
        private PaymentProcessingRepository      $paymentProcessingRepository,
        private LoggerInterface                  $logger,
    ) {
    }

    public function __invoke(NotifySubscriber $message): void
    {
        try {
            $subscriber = $this->subscriberRepository->find($message->subscriberId);
            if (! $subscriber) {
                throw new EntityNotFoundException("Subscriber \"{$message->subscriberId}\" not found.");
            }

            $paymentProcessing = $this->paymentProcessingRepository->find($message->paymentProcessingId);
            if (! $paymentProcessing) {
                throw new EntityNotFoundException("Payment processing \"{$message->paymentProcessingId}\" not found.");
            }

            // TODO: check the order status and subscriber's expected order status.

            $this->sendSubscriberNotificationAction->sendNotification($subscriber, $paymentProcessing);
        } catch (EntityNotFoundException|ChannelMessageNotRegistered|NotificationChannelNotRegisteredException $e) {
            $this->logger->error($e->getMessage());
            return;
        }
    }
}
