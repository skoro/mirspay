<?php

declare(strict_types=1);

namespace App\Subscriber\Notification;

use App\Entity\Order;
use App\Payment\Common\Message\ResponseInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class HttpNotificationHandler implements NotificationHandlerInterface
{
    public function __construct(
        private HttpClientInterface $httpClient,
    ) {
    }

    /**
     * @param Order $order
     * @param ResponseInterface $response
     * @param array{url: string, method: string} $params
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function send(Order $order, ResponseInterface $response, array $params): void
    {
        $url = $params['url'] ?? '';
        $method = $params['method'] ?? '';

        $data = [
            'order_num' => $order->getExternalOrderId(),
            'order_status' => $order->getStatus()->value,
            'response' => $response,
        ];

        $this->httpClient->request($method, $url, [
            'json' => $data,
        ]);
    }
}
