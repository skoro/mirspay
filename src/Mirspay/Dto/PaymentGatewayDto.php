<?php

declare(strict_types=1);

namespace Mirspay\Dto;

use Mirspay\Payment\Common\GatewayInterface;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\SerializedName;

#[OA\Schema(
    description: 'Payment Gateway object.'
)]
final readonly class PaymentGatewayDto
{
    public function __construct(
        #[OA\Property(description: 'Payment gateway id.')]
        public string $id,

        #[OA\Property(description: 'Payment gateway name.')]
        public string $name,

        #[OA\Property(description: 'When test mode is positive, no real payments done.')]
        #[SerializedName('test_mode')]
        public bool $isTestMode,
    ) {
    }

    public static function createFromPaymentGateway(GatewayInterface $gateway): self
    {
        return new self(
            id: $gateway->getId(),
            name: $gateway->getName(),
            isTestMode: $gateway->isTestMode(),
        );
    }
}
