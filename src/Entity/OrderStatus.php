<?php

declare(strict_types=1);

namespace App\Entity;

enum OrderStatus: string
{
    case CREATED = 'created';
    case ERROR = 'error';
    case FINISHED = 'finished';
    case PAYMENT_PENDING = 'payment_pending';
    case PAYMENT_RECEIVED = 'payment_received';
    case PAYMENT_FAILED = 'payment_failed';
}
