<?php

declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Dto\PaymentGatewayDto;
use App\Entity\Order;
use App\Entity\OrderStatus;
use App\Order\Workflow\OrderWorkflowFactory;
use App\Payment\Common\GatewayInterface;
use App\Payment\PaymentGatewayRegistry;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/payment', name: 'api_v1_payment_')]
class PaymentController extends AbstractController
{
    public function __construct(
        private readonly PaymentGatewayRegistry $paymentGatewayRegistry,
    ) {
    }

    #[Route('/gateways', name: 'gateways', methods: 'GET', format: 'json')]
    public function getAvailableGateways(): JsonResponse
    {
        /** @var PaymentGatewayDto[] $gatewayCollection */
        $gatewayCollection = [];

        /** @var GatewayInterface $paymentGateway */
        foreach ($this->paymentGatewayRegistry as $paymentGateway) {
            $gatewayCollection[] = PaymentGatewayDto::createFromPaymentGateway($paymentGateway);
        }

        return $this->json($gatewayCollection);
    }

    #[Route('/{order_uuid}/handler', name: 'callback_handler', format: 'json')]
    public function paymentCallbackHandler(
        #[MapEntity(mapping: ['order_uuid' => 'uuid'])] Order $order,
        Request $request,
        OrderWorkflowFactory $orderWorkflowFactory,
    ): Response {
        $paymentGateway = $this->paymentGatewayRegistry->getGatewayById($order->getPaymentGateway());

        $serverCallbackHandler = $paymentGateway->getServerCallbackHandler();
        $response = $serverCallbackHandler->handleCallback($request->getContent(), $request->headers->all());

        $workflow = $orderWorkflowFactory->createFromContext($order, null, $response);

        if ($response->isSuccessful()) {
            $workflow->setState(OrderStatus::PAYMENT_RECEIVED);
        } else {
            $workflow->setState(OrderStatus::PAYMENT_FAILED);
        }

        return new Response();
    }

    #[Route('/{order_uuid}/status', name: 'status', format: 'json')]
    public function getPaymentStatus(
        #[MapEntity(mapping: ['order_uuid' => 'uuid'])] Order $order,
    ): JsonResponse {
        $paymentGateway = $this->paymentGatewayRegistry->getGatewayById($order->getPaymentGateway());

        $paymentStatusRequest = $paymentGateway->getPaymentStatusRequestBuilder()->build($order);

        $statusResponse = $paymentStatusRequest->send();

        // TODO: message "new PaymentStatusWasReceived($order->getId(), $statusResponse)"

        return $this->json([
            'payment_gateway' => $paymentGateway->getId(),
            'success' => $statusResponse->isSuccessful(),
            'transaction_id' => $statusResponse->getTransactionId(),
            'message' => $statusResponse->getMessage(),
            'code' => $statusResponse->getCode(),
            'data' => $statusResponse,
        ]);
    }
}
