<?php

declare(strict_types=1);

namespace Mirspay\Payment\Common;

use Mirspay\Payment\Common\Builder\PaymentStatusRequestBuilderInterface;
use Mirspay\Payment\Common\Builder\PurchaseRequestBuilderInterface;
use Mirspay\Payment\Common\Message\ServerCallbackHandlerInterface;

interface GatewayInterface
{
    /**
     * @return non-empty-string
     */
    public function getId(): string;

    /**
     * @return non-empty-string
     */
    public function getName(): string;

    /**
     * Whether the payment gateway is in test mode ?
     */
    public function isTestMode(): bool;

    public function getPurchaseRequestBuilder(): PurchaseRequestBuilderInterface;

    public function getPaymentStatusRequestBuilder(): PaymentStatusRequestBuilderInterface;

    public function getServerCallbackHandler(): ServerCallbackHandlerInterface;
}
