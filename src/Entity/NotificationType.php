<?php

declare(strict_types=1);

namespace App\Entity;

enum NotificationType: string
{
    case HTTP = 'http';
}
