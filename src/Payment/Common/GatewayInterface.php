<?php

declare(strict_types=1);

namespace App\Payment\Common;

use App\Payment\Common\Builder\PaymentStatusRequestBuilderInterface;
use App\Payment\Common\Builder\PurchaseRequestBuilderInterface;
use App\Payment\Common\Message\ServerCallbackHandlerInterface;

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

    public function isTestMode(): bool;

    public function getPurchaseRequestBuilder(): PurchaseRequestBuilderInterface;

    public function getPaymentStatusRequestBuilder(): PaymentStatusRequestBuilderInterface;

    public function getServerCallbackHandler(): ServerCallbackHandlerInterface;
}
