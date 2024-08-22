<?php

declare(strict_types=1);

namespace App\Payment\LiqPay\Request;

use App\Payment\Common\Message\MessageInterface;

/**
 * Request message data encoder.
 */
class MessageEncoder
{
    /**
     * @param MessageInterface $message A message to encode.
     * @return non-empty-string An encoded message.
     */
    public function encode(MessageInterface $message): string
    {
        return base64_encode(json_encode($message->getRawData()));
    }
}
