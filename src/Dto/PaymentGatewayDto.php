<?php

declare(strict_types=1);

namespace App\Dto;

use App\Payment\Common\GatewayInterface;
use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class PaymentGatewayDto
{
    public function __construct(
        public string $id,
        public string $name,
        #[SerializedName('test_mode')] public bool $isTestMode,
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
