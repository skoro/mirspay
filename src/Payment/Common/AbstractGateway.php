<?php

declare(strict_types=1);

namespace App\Payment\Common;

use App\Payment\Common\Message\RequestInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Abstract payment gateway.
 *
 * Example:
 *
 *     final class MyPaymentGateway extends AbstractGateway
 *     {
 *         public function getPurchaseRequestBuilder(): PurchaseRequestBuilderInterface
 *         {
 *              return new CommonPurchaseRequestBuilder(
 *                  $this->createRequest(MyPurchaseRequest::class)
 *              );
 *         }
 *     }
 *
 *     final class MyPurchaseRequest extends AbstractRequest
 *     {
 *         // ...
 *     }
 *
 *     $myPayment = new MyPaymentGateway($httpClient);
 *     $purchase = $myPayment->getPurchaseRequestBuilder()->build($order);
 *     $response = $purchase->send();
 *     $response->isSuccessful();
 */
abstract class AbstractGateway implements GatewayInterface
{
    public function __construct(
        protected readonly HttpClientInterface $httpClient,
    ) {
    }

    /**
     * Creates and initializes a payment request.
     *
     * @param class-string<RequestInterface> $requestClass The request class to create.
     * @param mixed ...$args The additional arguments for request class constructor.
     */
    protected function createRequest(string $requestClass, mixed ...$args): RequestInterface
    {
        /** @var RequestInterface $request */
        $request = new $requestClass($this->httpClient, ...$args);

        assert($request instanceof RequestInterface);

        // FIXME: the request is also initialized in a builder, so it will be initialized twice.
        $request->initialize();

        return $request;
    }
}
