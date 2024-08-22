<?php

declare(strict_types=1);

namespace App\Order;

use App\Dto\ProductDto;
use Money\Currency;
use Money\Money;

/**
 * Calculates a total order amount based on products and their quantity.
 */
class OrderTotalAmountCalculator
{
    private Currency $defaultCurrency;

    public function __construct(string | Currency $currencyCode)
    {
        $this->defaultCurrency = \is_string($currencyCode)
            ? new Currency($currencyCode) : $currencyCode;
    }

    /**
     * @param ProductDto|ProductDto[] $products A product or list of products of DTO type calculating the total order price.
     * @param Currency|null $currency Override default currency.
     * @return Money The total order price.
     */
    public function calcTotalOfProductDto(ProductDto | array $products, ?Currency $currency = null): Money
    {
        return $this->calculate(
            \is_array($products) ? $products : [$products],
            static fn (ProductDto $product) => $product->price,
            static fn (ProductDto $product) => $product->qty,
            $currency
        );
    }

    /**
     * @param array<object> $products A list of product objects.
     * @param callable(object): int $productPriceCallback A callback returning the product price in integer.
     * @param callable(object): int $productQuantityCallback A callback returning the product quantity.
     */
    private function calculate(
        array $products,
        callable $productPriceCallback,
        callable $productQuantityCallback,
        Currency | null $currency = null,
    ): Money {
        $currency ??= $this->defaultCurrency;

        $total = new Money(0, $currency);

        foreach ($products as $product) {
            $productPrice = new Money($productPriceCallback($product), $currency);
            $total = $total->add($productPrice->multiply($productQuantityCallback($product)));
        }

        return $total;
    }
}
