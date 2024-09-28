<?php

declare(strict_types=1);

namespace Mirspay\Subscriber\Channel;

use Mirspay\Entity\PaymentProcessing;
use Mirspay\Subscriber\Exception\ChannelMessageException;

abstract class AbstractChannelMessage implements ChannelMessageInterface
{
    private PaymentProcessing | null $paymentProcessing = null;

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
