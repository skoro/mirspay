<?php

declare(strict_types=1);

namespace Mirspay\Subscriber\Channel;

use Mirspay\Subscriber\Exception\ChannelMessageException;
use Mirspay\Subscriber\Exception\NotificationChannelException;

/**
 * Notification channel.
 *
 * Sends a notification message to a subscriber. A channel is transport for delivering the message.
 *
 * Custom channel, for example SMS notifications:
 *
 *      final class MyCustomSmsChannel implements NotificationChannelInterface
 *      {
 *          private readonly SmsTransport $sms;
 *
 *          // sms transport initialization, so on ...
 *
 *          public function send(ChannelMessageInterface $message, array $params): void
 *          {
 *              $this->sms->send($message->getData(), $params['phone_number']);
 *          }
 *      }
 *
 * In `config/services.yml` the custom channel:
 *
 *       services:
 *         App\Subscriber\Channel\MyCustomSmsChannel:
 *           tags:
 *             - { name: 'app.subscriber.channel', type: 'sms' }
 *
 * Then, the `sms` custom channel will be available in `subscriber:channels` command output under `channels`.
 * The adding subscriber command should be implemented too, in order to add subscribers.
 */
interface NotificationChannelInterface
{
    /**
     * @param array<string, mixed> $params Channel parameters.
     *
     * @throws NotificationChannelException
     * @throws ChannelMessageException
     */
    public function send(ChannelMessageInterface $message, array $params): void;
}
