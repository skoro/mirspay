<?php

declare(strict_types=1);

namespace Mirspay\Subscriber\Channel;

use Mirspay\Subscriber\Exception\ChannelMessageException;

final class SimpleArrayChannelMessage extends AbstractChannelMessage
{
    /**
     * @return array{
     *     order_num: string,
     *     order_status: string,
     *     success: bool,
     *     response: array
     * }
     * @throws ChannelMessageException
     */
    public function getData(): array
    {
        $paymentProcessing = $this->getPaymentProcessing();
        $order = $paymentProcessing->getOrder();

        return [
            'order_num' => $order->getExternalOrderId(),
            'order_status' => $order->getStatus()->value,
            'success' => $paymentProcessing->getResponseSuccess(),
            // TODO: a column in payment_processing should be added.
            //'transaction_id' => $this->getResponse()->getTransactionId(),
            'response' => $paymentProcessing->getResponseData(),
        ];
    }
}
