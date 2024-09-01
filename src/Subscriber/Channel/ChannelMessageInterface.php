<?php

declare(strict_types=1);

namespace App\Subscriber\Channel;

use App\Entity\Order;
use App\Payment\Common\Message\ResponseInterface;
use App\Subscriber\Exception\ChannelMessageException;

/**
 * Channel data message.
 *
 * A channel uses the message to get (format) the data and send it to a subscriber via notification.
 */
interface ChannelMessageInterface
{
    public function setOrder(Order $order): void;

    /**
     * @throws ChannelMessageException When order is not set.
     */
    public function getOrder(): Order;

    public function setResponse(ResponseInterface $response): void;

    /**
     * @throws ChannelMessageException When payment gateway response is not set.
     */
    public function getResponse(): ResponseInterface;

    /**
     * Returns a data that will be transmitted by a channel.
     *
     * @throws ChannelMessageException
     */
    public function getData(): mixed;
}
