<?php

declare(strict_types=1);

namespace App\Subscriber\Channel;

use App\Entity\PaymentProcessing;
use App\Subscriber\Exception\ChannelMessageException;

final class SimpleArrayChannelMessage implements ChannelMessageInterface
{
    private PaymentProcessing | null $paymentProcessing = null;

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

    public function setPaymentProcessing(PaymentProcessing $paymentProcessing): void
    {
        $this->paymentProcessing = $paymentProcessing;
    }

    public function getPaymentProcessing(): PaymentProcessing
    {
        return $this->paymentProcessing
            ?? throw new ChannelMessageException('Payment processing response not set.');
    }
}
