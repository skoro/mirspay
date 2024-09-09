<?php

declare(strict_types=1);

namespace App\Payment\LiqPay\Response;

final class PaymentStatusResponse extends AbstractResponse
{
    public const string SUCCESS = 'success';

    public function isRedirect(): false
    {
        return false;
    }

    public function isSuccessful(): bool
    {
        $status = $this->getStatus();

        return $status === self::SUCCESS;
    }

    public function getMessage(): string
    {
        $message = $this->data['err_description'] ?? '';

        return (string) $message;
    }

    public function getTransactionId(): string
    {
        $transactionId = $this->data['transaction_id'] ?? '';

        return (string) $transactionId;
    }

    public function getPaymentId(): string
    {
        return (string) ($this->data['payment_id'] ?? '');
    }

    public function getRawData(): array
    {
        return $this->data;
    }
}
