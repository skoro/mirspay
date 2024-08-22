<?php

declare(strict_types=1);

namespace App\Payment\LiqPay;

use SensitiveParameter;

/**
 * Implementation of LiqPay signature algorithm.
 */
class Signature
{
    public function __construct(
        #[SensitiveParameter] private readonly string $privateKey,
    ) {
    }

    /**
     * Whether a signature for the provided data string is valid.
     *
     * @param non-empty-string $str The data string (usually encoded in base64 encoding).
     * @param non-empty-string $signature The signature for validation.
     */
    public function isValid(string $str, string $signature): bool
    {
        return $this->make($str) === $signature;
    }

    /**
     * Makes a signature of the provided data string.
     *
     * @param non-empty-string $str
     * @return non-empty-string
     */
    public function make(string $str): string
    {
        $data = $this->privateKey . $str . $this->privateKey;

        return base64_encode(sha1($data, binary: true));
    }
}
