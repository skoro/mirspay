<?php

declare(strict_types=1);

namespace Mirspay\Tests\Unit\Subscriber;

use Mirspay\Entity\Order;
use Mirspay\Entity\OrderStatus;
use Mirspay\Entity\PaymentProcessing;
use Mirspay\Payment\Common\Message\ResponseInterface;
use Mirspay\Subscriber\Channel\SimpleArrayChannelMessage;
use Mirspay\Tests\Concerns\WithFaker;
use PHPUnit\Framework\TestCase;

final class SimpleArrayChannelMessageTest extends TestCase
{
    use WithFaker;

    public function testMessageData(): void
    {
        $message = new SimpleArrayChannelMessage();

        $order = new Order();
        $order->setExternalOrderId($this->faker()->md5());
        $order->setStatus(OrderStatus::PAYMENT_FAILED);

        $response = $this->createStub(ResponseInterface::class);
        $response->method('isSuccessful')->willReturn(false);
        $response->method('jsonSerialize')->willReturn([
            'a' => 1,
            'b' => 2,
        ]);

        $paymentProcessing = PaymentProcessing::create($order, null, $response);

        $message->setPaymentProcessing($paymentProcessing);

        $data = $message->getData();

        $this->assertEquals([
            'order_num' => $order->getExternalOrderId(),
            'order_status' => 'payment_failed',
            'success' => false,
            'response' => [
                'a' => 1,
                'b' => 2,
            ],
        ], $data);
    }
}
