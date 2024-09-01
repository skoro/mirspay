<?php

declare(strict_types=1);

namespace App\Subscriber\Channel;

use App\Entity\Order;
use App\Payment\Common\Message\ResponseInterface;
use App\Subscriber\Exception\ChannelMessageException;

final class SimpleArrayChannelMessage implements ChannelMessageInterface
{
    private Order | null $order = null;
    private ResponseInterface | null $response = null;

    /**
     * @return array{
     *     order_num: string,
     *     order_status: string,
     *     success: bool,
     *     transaction_id: string,
     *     response: array
     * }
     * @throws ChannelMessageException
     */
    public function getData(): array
    {
        return [
            'order_num' => $this->getOrder()->getExternalOrderId(),
            'order_status' => $this->getOrder()->getStatus()->value,
            'success' => $this->getResponse()->isSuccessful(),
            'transaction_id' => $this->getResponse()->getTransactionId(),
            'response' => $this->getResponse(),
        ];
    }

    public function setOrder(Order $order): void
    {
        $this->order = $order;
    }

    public function getOrder(): Order
    {
        return $this->order
            ?? throw new ChannelMessageException('Order not set');
    }

    public function setResponse(ResponseInterface $response): void
    {
        $this->response = $response;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response
            ?? throw new ChannelMessageException('Payment gateway response not set.');
    }
}
