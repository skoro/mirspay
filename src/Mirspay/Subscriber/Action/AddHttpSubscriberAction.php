<?php

declare(strict_types=1);

namespace Mirspay\Subscriber\Action;

use InvalidArgumentException;
use Mirspay\Entity\OrderStatus;
use Mirspay\Entity\Subscriber;
use Mirspay\Subscriber\Channel\HttpNotificationChannel;

class AddHttpSubscriberAction extends AddSubscriberAction
{
    public function add(
        OrderStatus $orderStatus,
        string      $url,
        string      $channelMessage,
        string      $httpMethod = HttpNotificationChannel::DEFAULT_HTTP_METHOD,
    ): Subscriber {
        if (! in_array($httpMethod, HttpNotificationChannel::HTTP_METHODS, true)) {
            throw new InvalidArgumentException(
                sprintf('Invalid http method "%s", must be one of [%s]',
                    $httpMethod,
                    implode(',', HttpNotificationChannel::HTTP_METHODS)
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

        return $this->addSubscriber(
            orderStatus: $orderStatus,
            channelType: 'http',
            channelMessage: $channelMessage,
            params: $params,
        );
    }
}
