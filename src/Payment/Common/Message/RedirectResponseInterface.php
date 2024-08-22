<?php

namespace App\Payment\Common\Message;

interface RedirectResponseInterface extends ResponseInterface
{
    /**
     * @see ResponseInterface::isRedirect()
     * @return non-empty-string
     */
    public function getRedirectUrl(): string;
}
