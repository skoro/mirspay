<?php

declare(strict_types=1);

namespace App\Subscriber\Channel;

use App\Entity\PaymentProcessing;
use App\Subscriber\Exception\ChannelMessageException;

/**
 * Channel data message.
 *
 * A channel uses the message to get (format) the data and send it to a subscriber via notification.
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
