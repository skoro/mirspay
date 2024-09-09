<?php

declare(strict_types=1);

namespace Mirspay\Tests\Concerns;

use Mirspay\Dto\ProductDto;

trait WithProductDto
{
    use WithFaker;

    /**
     * @param array{sku?: string, name?: string, qty?: int, price?: int} $data
     */
    private function makeProductDto(array $data = []): ProductDto
    {
        $defaults = [
            'sku' => $this->faker()->regexify('[A-Z0-9]{6,10}'),
            'name' => $this->faker()->words(3, asText: true),
            'qty' => $this->faker()->numberBetween(1, 10),
            'price' => $this->faker()->randomNumber(6),
        ];

        $data = array_merge($defaults, $data);

        return new ProductDto(...$data);
    }
}
