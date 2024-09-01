<?php

declare(strict_types=1);

namespace App\Subscriber\Channel;

use App\Entity\Order;
use App\Payment\Common\Message\ResponseInterface;

/**
 * Channel data message.
 *
 * A channel uses the message to get (format) the data and send it to a subscriber via notification.
 */
interface ChannelMessageInterface
{
    public function setOrder(Order $order): void;

    public function getOrder(): Order | null;

    public function setResponse(ResponseInterface $response): void;

    public function getResponse(): ResponseInterface | null;

    public function getData(): mixed;
}
