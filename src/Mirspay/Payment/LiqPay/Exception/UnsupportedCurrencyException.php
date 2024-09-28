<?php

declare(strict_types=1);

namespace Mirspay\Payment\LiqPay\Exception;

use Mirspay\Payment\Common\Exception\Exception;
use Mirspay\Payment\LiqPay\Request\AbstractRequest;
use Money\Currency;

class UnsupportedCurrencyException extends Exception
{
    public function __construct(
        public readonly Currency $currency
    ) {
        parent::__construct(
            sprintf('Expected one of [%s] currency but got "%s".',
                implode(', ', AbstractRequest::SUPPORTED_CURRENCIES),
                $currency->getCode()
            )
        );
    }
}
