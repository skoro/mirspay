<?php

declare(strict_types=1);

namespace App\Action;

use App\Entity\NotificationType;
use App\Entity\OrderStatus;
use App\Entity\Subscriber;
use InvalidArgumentException;

class AddHttpSubscriberAction extends AddSubscriberAction
{
    const array HTTP_METHODS = ['POST', 'PUT', 'PATCH'];
    const string DEFAULT_HTTP_METHOD = 'POST';

    public function add(
        OrderStatus $orderStatus,
        string      $url,
        string      $httpMethod = self::DEFAULT_HTTP_METHOD,
    ): Subscriber {
        if (! in_array($httpMethod, self::HTTP_METHODS, true)) {
            throw new InvalidArgumentException(
                sprintf('Invalid http method "%s", must be one of [%s]',
                    $httpMethod,
                    implode(',', self::HTTP_METHODS)
                )
            );
        }

        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException("Invalid url \"{$url}\".");
        }

        $params = [
            'url' => $url,
            'method' => $httpMethod,
        ];

        return $this->addSubscriber($orderStatus, NotificationType::HTTP, $params);
    }
}
