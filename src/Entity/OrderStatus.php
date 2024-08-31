<?php

declare(strict_types=1);

namespace App\Entity;

enum OrderStatus: string
{
    case CREATED = 'created';
    case PAYMENT_PENDING = 'payment_pending';
    case PAYMENT_RECEIVED = 'payment_received';
    case PAYMENT_FAILED = 'payment_failed';

    public static function formattedString(string $delimiter = ', '): string
    {
        return implode($delimiter, array_map(fn ($enum) => $enum->value, self::cases()));
    }
}
