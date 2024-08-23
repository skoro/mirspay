<?php

declare(strict_types=1);

namespace App\Payment\Common\Message;

use App\Payment\Common\Exception\InvalidRequestException;
use App\Payment\Common\Exception\RequestParameterRequiredException;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface as HttpResponse;

abstract class AbstractRequest implements RequestInterface
{
    protected readonly ParameterBag $parameters;

    public function __construct(
        protected readonly HttpClientInterface $httpClient,
        ParameterBag | null $parameters = null,
    ) {
        $this->parameters = $parameters ?? new ParameterBag();
    }

    public function initialize(): void
    {
    }

    /**
     * @return non-empty-string The request http method.
     *
     * @see static::doRequest()
     */
    public function getHttpMethod(): string
    {
        return 'POST';
    }

    /**
     * @return non-empty-string The request api url.
     *
     * @see static::doRequest()
     */
    abstract public function getRequestUrl(): string;

    /**
     * Validates the request before sending.
     *
     * @throws InvalidRequestException The request is not valid.
     * @throws RequestParameterRequiredException The required parameter value is not set.
     *
     * @see static::send()
     * @see static::validateParameters()
     */
    abstract public function validate(): void;

    /**
     * Validates and sends the request.
     *
     * @throws InvalidRequestException
     * @throws TransportExceptionInterface
     * @throws RequestParameterRequiredException
     */
    public function send(): ResponseInterface
    {
        $this->validate();

        $response = $this->doRequest();

        return $this->createResponse($response);
    }

    /**
     * Sends a http request to a payment gateway end-point.
     *
     * @throws TransportExceptionInterface
     */
    protected function doRequest(): HttpResponse
    {
        return $this->httpClient->request(
            method: $this->getHttpMethod(),
            url: $this->getRequestUrl(),
            options: $this->getHttpRequestOptions(),
        );
    }

    abstract protected function createResponse(HttpResponse $response): ResponseInterface;

    /**
     * @return array{body: mixed}
     *
     * @see static::doRequest()
     */
    protected function getHttpRequestOptions(): array
    {
        return [
            'body' => $this->getRawData(),
        ];
    }

    /**
     * @param non-empty-string $key
     */
    public function setParameter(string $key, mixed $value): static
    {
        $this->parameters->set($key, $value);

        return $this;
    }

    /**
     * @param non-empty-string $key
     */
    public function getParameter(string $key, mixed $default = null): mixed
    {
        return $this->parameters->get($key, $default);
    }

    /**
     * Validates the request parameters.
     *
     * Example:
     *
     *     public function validate(): void
     *     {
     *        $this->validateParameters('order_id', 'amount');
     *     }
     *
     * @param string ...$args Parameter keys to validate.
     * @throws RequestParameterRequiredException The required parameter value is not set.
     * @see static::validate()
     */
    public function validateParameters(string ...$args): void
    {
        foreach ($args as $key) {
            $value = $this->parameters->get($key);
            if ($value === null || $value === '') {
                throw new RequestParameterRequiredException($key);
            }
        }
    }

    protected function isValidUrl(string $url): bool
    {
        if ($url === '') {
            return false;
        }

        $validator = Validation::createValidator();
        $urlConstraint = new Assert\Url();

        $errors = $validator->validate($url, $urlConstraint);

        return $errors->count() === 0;
    }

    public function getRawData(): array
    {
        return $this->parameters->all();
    }

    public function jsonSerialize(): array
    {
        return $this->parameters->all();
    }
}