<?php

declare(strict_types=1);

namespace Mirspay\Dto;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class ProductDto
{
    public function __construct(
        #[OA\Property(description: 'Product SKU', example: 'BGD-1043Y')]
        #[Assert\Length(min: 2, max: 255)]
        public string $sku,

        #[OA\Property(description: 'Product name', example: 'T-Shirt')]
        #[Assert\Length(min: 2, max: 255)]
        public string $name,

        #[OA\Property(description: 'Quantity', example: 1)]
        #[Assert\Positive]
        public int $qty,

        #[OA\Property(description: 'Price (integer)', example: 100_00)]
        #[Assert\PositiveOrZero]
        public int $price,
    ) {
    }
}
