<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class OrderDto
{
    /**
     * @param array<ProductDto> $products
     */
    public function __construct(
        #[Assert\Length(min:2, max: 255)]
        #[SerializedName('order_num')]
        public string $orderNum,

        #[Assert\Length(min: 2, max: 16)]
        #[SerializedName('payment_gateway')]
        public string $paymentGateway,

        #[Assert\Length(min: 2, max: 255)]
        public string $description,

        #[Assert\Count(min: 1, max: 100)]
        #[Assert\Valid]
        public array $products,

        #[Assert\Url]
        #[Assert\Length(max: 1000)]
        #[SerializedName('return_url')]
        public string $returnUrl,
    ) {
    }
}
