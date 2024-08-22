<?php

declare(strict_types=1);

namespace App\Payment\Common\Message;

use Exception;

/**
 * A dump request that does nothing.
 *
 * It is used for creating a responses for which sending a real request is not needed.
 */
final readonly class NullRequest implements RequestInterface
{
    public function initialize(): void
    {
    }

    /**
     * @throws Exception It is not a real request.
     */
    public function send(): ResponseInterface
    {
        $this->throwNotImplemented();
    }

    public function getRawData(): null
    {
        return null;
    }

    public function jsonSerialize(): null
    {
        return null;
    }

    /**
     * @throws Exception
     */
    private function throwNotImplemented(): void
    {
        throw new Exception('Not implemented');
    }
}
