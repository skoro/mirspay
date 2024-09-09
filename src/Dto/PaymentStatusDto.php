<?php

declare(strict_types=1);

namespace App\Dto;

use App\Payment\Common\Message\ResponseInterface;
use Symfony\Component\Serializer\Attribute\SerializedName;
use OpenApi\Attributes as OA;

#[OA\Schema(
    description: 'Payment status object.',
)]
final readonly class PaymentStatusDto
{
    public function __construct(
        #[OA\Property(description: 'Payment gateway id.')]
        #[SerializedName('payment_gateway')]
        public string $paymentGatewayId,

        #[OA\Property(description: 'Transaction result.')]
        #[SerializedName('success')]
        public bool $isSuccess,

        #[OA\Property(description: 'Transaction id from the payment gateway side.')]
        #[SerializedName('transaction_id')]
        public string $transactionId,

        #[OA\Property(description: 'An error message.')]
        public string $message,

        #[OA\Property(description: 'An error code.')]
        public string $code,

        #[OA\Property(description: 'A payment status transaction as it was received from the payment gateway.')]
        public array $data,
    ) {
    }

    public static function makeFromResponse(
        string $paymentGatewayId,
        ResponseInterface $response,
    ): self {
        return new self(
            paymentGatewayId: $paymentGatewayId,
            isSuccess: $response->isSuccessful(),
            transactionId: $response->getTransactionId(),
            message: (string) $response->getMessage(),
            code: (string) $response->getCode(),
            data: (array) $response->getRawData(),
        );
    }
}
