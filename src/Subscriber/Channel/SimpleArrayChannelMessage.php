<?php

declare(strict_types=1);

namespace App\Subscriber\Channel;

use App\Entity\Order;
use App\Payment\Common\Message\ResponseInterface;

final class SimpleArrayChannelMessage implements ChannelMessageInterface
{
    private Order | null $order;
    private ResponseInterface | null $response;

    public function getData(): array
    {
        return [
            'order_num' => $this->order?->getExternalOrderId(),
            'order_status' => $this->order?->getStatus()->value,
            'success' => $this->response?->isSuccessful(),
            'response' => $this->response,
        ];
    }

    public function setOrder(Order $order): void
    {
        $this->order = $order;
    }

    public function getOrder(): Order | null
    {
        return $this->order;
    }

    public function setResponse(ResponseInterface $response): void
    {
        $this->response = $response;
    }

    public function getResponse(): ResponseInterface | null
    {
        return $this->response;
    }
}
