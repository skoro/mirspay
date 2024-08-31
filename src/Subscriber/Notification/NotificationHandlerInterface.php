<?php

declare(strict_types=1);

namespace App\Subscriber\Notification;

use App\Entity\Order;
use App\Payment\Common\Message\ResponseInterface;

interface NotificationHandlerInterface
{
    public function send(Order $order, ResponseInterface $response, array $params): void;
}
