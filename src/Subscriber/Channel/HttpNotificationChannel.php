<?php

declare(strict_types=1);

namespace App\Subscriber\Channel;

use App\Entity\Order;
use App\Payment\Common\Message\ResponseInterface;
use App\Subscriber\Exception\ChannelMessageException;
use App\Subscriber\Exception\NotificationChannelException;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
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
     * @param array{url: string, method: string} $params
     *
     * @throws TransportExceptionInterface
     * @throws NotificationChannelException
     * @throws ChannelMessageException
     */
    public function send(ChannelMessageInterface $message, array $params): void
    {
        $url = $params['url'] ?? '';
        $method = $this->getHttpMethod($params);

        $this->httpClient->request($method, $url, [
            'json' => $message->getData(),
        ]);
    }

    /**
     * @param array{method: string} $params
     * @throws NotificationChannelException
     */
    private function getHttpMethod(array $params): string
    {
        $method = (string) ($params['method'] ?? self::DEFAULT_HTTP_METHOD);

        if (! in_array($method, self::HTTP_METHODS)) {
            throw new NotificationChannelException("Http method \"$method\" is not allowed.");
        }

        return $method;
    }
}
