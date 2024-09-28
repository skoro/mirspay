<?php

declare(strict_types=1);

namespace Mirspay\Entity;

use BackedEnum;

enum OrderStatus: string
{
    case CREATED = 'created';
    case PAYMENT_PENDING = 'payment_pending';
    case PAYMENT_RECEIVED = 'payment_received';
    case PAYMENT_FAILED = 'payment_failed';

    public static function formattedString(string $delimiter = ', '): string
    {
        return implode($delimiter, array_map(fn (BackedEnum $enum): string => $enum->value, self::cases()));
    }
}
