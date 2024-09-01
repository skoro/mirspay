<?php

declare(strict_types=1);

namespace App\Subscriber\Channel;

use App\Entity\Order;
use App\Payment\Common\Message\ResponseInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class HttpNotificationChannel implements NotificationChannelInterface
{
    public const array HTTP_METHODS = ['POST', 'PUT', 'PATCH'];
    public const string DEFAULT_HTTP_METHOD = 'POST';

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
    public function send(ChannelMessageInterface $message, array $params): void
    {
        $url = $params['url'] ?? '';
        $method = $params['method'] ?? '';

        $this->httpClient->request($method, $url, [
            'json' => [],
        ]);
    }
}
