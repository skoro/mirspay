<?php

declare(strict_types=1);

namespace App\Payment\Common\Message;

use App\Payment\Common\Exception\InvalidResponseException;

/**
 * Handles the payment gateway callback.
 */
interface ServerCallbackHandlerInterface
{
    /**
     * @param mixed $data A raw data of the payment gateway response.
     * @param array $params Additional parameters of the response (headers, etc.).
     * @param RequestInterface|null $request A request initiated the callback.
     * @return ResponseInterface
     * @throws InvalidResponseException Something wrong with the response.
     */
    public function handleCallback(
        mixed $data,
        array $params = [],
        RequestInterface | null $request = null,
    ): ResponseInterface;
}
