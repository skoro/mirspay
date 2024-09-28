<?php

declare(strict_types=1);

namespace Mirspay\Tests\Concerns;

trait WithOrderPostData
{
    private function makeOrderPostData(array $data = []): array
    {
        return array_merge([
            'order_num' => '12345678',
            'payment_gateway' => 'test',
            'description' => 'Post order',
            'return_url' => 'https://super-site.com/thank-you',
            'products' => [
                [
                    'sku' => '89076',
                    'name' => 'Product Test',
                    'price' => 785_64,
                    'qty' => 3,
                ],
            ],
        ], $data);
    }
}
