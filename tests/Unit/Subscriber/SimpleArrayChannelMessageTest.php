<?php

declare(strict_types=1);

namespace App\Tests\Unit\Subscriber;

use App\Entity\Order;
use App\Entity\OrderStatus;
use App\Entity\PaymentProcessing;
use App\Payment\Common\Message\ResponseInterface;
use App\Subscriber\Channel\SimpleArrayChannelMessage;
use App\Tests\Concerns\WithFaker;
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
