<?php

declare(strict_types=1);

namespace App\Payment\Common\Message;

interface RequestInterface extends MessageInterface
{
    /**
     * Initialize the request.
     */
    public function initialize(): void;

    /**
     * Send the request to the payment gateway.
     */
    public function send(): ResponseInterface;
}
