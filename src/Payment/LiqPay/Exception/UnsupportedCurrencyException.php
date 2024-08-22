<?php

declare(strict_types=1);

namespace App\Payment\LiqPay\Exception;

use App\Payment\Common\Exception\Exception;
use App\Payment\LiqPay\Request\AbstractRequest;
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
