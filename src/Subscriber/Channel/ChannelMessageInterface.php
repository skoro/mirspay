<?php

declare(strict_types=1);

namespace App\Subscriber\Channel;

use App\Entity\PaymentProcessing;
use App\Subscriber\Exception\ChannelMessageException;

/**
 * Channel data message.
 *
 * A channel uses a message to get (format) the data and send it to a subscriber via notification.
 *
 * Message classes must implement this interface in order be sent via channel.
 * Also, it must be tagged as 'app.subscriber.message' with the appropriate type.
 *
 * For example, a custom message serializes data to JSON:
 *
 *      final class JsonChannelMessage extends AbstractChannelMessage
 *      {
 *        public function getData(): string
 *        {
 *            return json_encode([
 *              'order_num' => $this->getPaymentProcessing()->getOrder()->getExternalOrderId(),
 *            ]);
 *        }
 *      }
 *
 *  In `config/services.yml` the custom channel:
 *
 *      services:
 *        App\Subscriber\Channel\JsonChannelMessage:
 *          tags:
 *            - { name: 'app.subscriber.message', type: 'json' }
 *
 *  Then, the `json` custom message will be available in `subscriber:channels` command output.
 */
interface ChannelMessageInterface
{
    public function setPaymentProcessing(PaymentProcessing $paymentProcessing): void;

    /**
     * @throws ChannelMessageException When payment processing is not set.
     */
    public function getPaymentProcessing(): PaymentProcessing;

    /**
     * Returns a data that will be transmitted by a channel.
     *
     * @throws ChannelMessageException
     */
    public function getData(): mixed;
}
