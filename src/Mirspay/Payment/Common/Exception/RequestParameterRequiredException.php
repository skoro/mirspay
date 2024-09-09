<?php

declare(strict_types=1);

namespace Mirspay\Payment\Common\Exception;

class RequestParameterRequiredException extends InvalidRequestException
{
    public function __construct(
        public readonly string $parameterName,
        string $message = '',
    ) {
        parent::__construct(
            $message ?: "The parameter '$this->parameterName' is required."
        );
    }
}
