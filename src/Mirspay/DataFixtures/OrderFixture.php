<?php

declare(strict_types=1);

namespace Mirspay\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Mirspay\Dto\OrderDto;
use Mirspay\Dto\ProductDto;
use Mirspay\Entity\Order;
use Mirspay\Order\OrderTotalAmountCalculator;

final class OrderFixture extends Fixture
{
    public function __construct(
        private readonly OrderTotalAmountCalculator $orderAmountCalculator,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $manager->persist($this->orderWithTestGateway());
        $manager->persist($this->orderWithLiqpayGateway());

        $manager->flush();
    }

    private function orderWithTestGateway(): Order
    {
        $orderDto = new OrderDto(
            orderNum: '111-test',
            paymentGateway: 'test-gateway',
            description: 'Test Gateway Order',
            products: [
                new ProductDto(
                    sku: 'DR94TY',
                    name: 'Golden Spoon',
                    qty: 1,
                    price: 1250_80,
                ), new ProductDto(
                    sku: 'FGH890',
                    name: 'Brown Mug',
                    qty: 3,
                    price: 500_00,
                ),
            ],
            returnUrl: 'https://example.com/thank-you-for-your-order',
        );

        return Order::createFromOrderDto($orderDto, $this->orderAmountCalculator);
    }

    private function orderWithLiqpayGateway(): Order
    {
        $orderDto = new OrderDto(
            orderNum: '111-liqpay',
            paymentGateway: 'liqpay',
            description: 'Test Liqpay Order',
            products: [
                new ProductDto(
                    sku: 'DR94TY',
                    name: 'Golden Spoon',
                    qty: 1,
                    price: 1250_80,
                ), new ProductDto(
                    sku: 'FGH890',
                    name: 'Brown Mug',
                    qty: 3,
                    price: 500_00,
                ),
            ],
            returnUrl: 'https://example.com/thank-you-for-your-order',
        );

        return Order::createFromOrderDto($orderDto, $this->orderAmountCalculator);
    }
}
