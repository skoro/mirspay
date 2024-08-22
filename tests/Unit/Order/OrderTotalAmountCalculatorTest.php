<?php

declare(strict_types=1);

namespace App\Tests\Unit\Order;

use App\Order\OrderTotalAmountCalculator;
use App\Tests\Concerns\WithFaker;
use App\Tests\Concerns\WithProductDto;
use Money\Currency;
use PHPUnit\Framework\TestCase;

final class OrderTotalAmountCalculatorTest extends TestCase
{
    use WithProductDto;
    use WithFaker;

    public function testCalculatesProductsTotalAmount(): void
    {
        $productDto1 = $this->makeProductDto([
            'qty' => 1,
            'price' => 4_50,
        ]);
        $productDto2 = $this->makeProductDto([
            'qty' => 5,
            'price' => 200_00,
        ]);
        $calculator = new OrderTotalAmountCalculator($this->faker()->currencyCode());

        $money = $calculator->calcTotalOfProductDto([$productDto1, $productDto2]);

        $this->assertEquals('100450', $money->getAmount());
    }

    public function testCalculationWithoutCurrencyUsesDefaultOne(): void
    {
        $defaultCurrency = $this->faker()->currencyCode();
        $productDto = $this->makeProductDto();
        $calculator = new OrderTotalAmountCalculator($defaultCurrency);

        $money = $calculator->calcTotalOfProductDto($productDto);

        $this->assertEquals($defaultCurrency, $money->getCurrency()->getCode());
    }

    public function testOverrideDefaultCurrency(): void
    {
        $defaultCurrency = 'USD';
        $productDto = $this->makeProductDto();
        $productCurrency = new Currency('DM');
        $calculator = new OrderTotalAmountCalculator($defaultCurrency);

        $money = $calculator->calcTotalOfProductDto([$productDto], $productCurrency);

        $this->assertEquals($productCurrency, $money->getCurrency()->getCode());
    }
}
