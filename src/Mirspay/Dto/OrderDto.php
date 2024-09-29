<?php

declare(strict_types=1);

namespace Mirspay\Dto;

use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(
    description: 'A data object used for creating an order.',
)]
final readonly class OrderDto
{
    /**
     * @param array<ProductDto> $products
     */
    public function __construct(
        #[OA\Property(description: 'Order number.', example: '8420910032')]
        #[Assert\Length(min:2, max: 255)]
        #[SerializedName('order_num')]
        public string $orderNum,

        #[OA\Property(description: 'Which payment gateway use for order purchase.', example: 'liqpay')]
        #[Assert\Length(min: 2, max: 16)]
        #[SerializedName('payment_gateway')]
        public string $paymentGateway,

        #[OA\Property(description: 'Description of the order.', example: 'Order #00102')]
        #[Assert\Length(min: 2, max: 255)]
        public string $description,

        #[OA\Property(description: 'List of the order products.')]
        #[Assert\Count(min: 1, max: 100)]
        #[Assert\Valid]
        public array $products,

        #[OA\Property(description: 'Webpage url to return a customer after filling a payment form.', example: 'https://your-url.com/thank-you-page')]
        #[Assert\Url]
        #[Assert\Length(max: 1000)]
        #[SerializedName('return_url')]
        public string $returnUrl,
    ) {
    }
}
