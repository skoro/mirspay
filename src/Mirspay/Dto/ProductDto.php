<?php

declare(strict_types=1);

namespace Mirspay\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class ProductDto
{
    public function __construct(
        #[Assert\Length(min: 2, max: 255)]
        public string $sku,

        #[Assert\Length(min: 2, max: 255)]
        public string $name,

        #[Assert\Positive]
        public int $qty,

        #[Assert\PositiveOrZero]
        public int $price,
    ) {
    }
}
