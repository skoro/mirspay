<?php

declare(strict_types=1);

namespace App\Payment\Common\Message;

/**
 * Redirect response.
 *
 * For example, a redirect to payment form.
 */
interface RedirectResponseInterface extends ResponseInterface
{
    /**
     * @see ResponseInterface::isRedirect()
     * @return non-empty-string
     */
    public function getRedirectUrl(): string;
}
