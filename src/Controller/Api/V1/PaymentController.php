<?php

declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Dto\PaymentGatewayDto;
use App\Dto\PaymentStatusDto;
use App\Entity\Order;
use App\Entity\OrderStatus;
use App\Event\AfterPaymentCallbackHandlerEvent;
use App\Event\BeforePaymentCallbackHandlerEvent;
use App\Event\PaymentStatusEvent;
use App\Order\Workflow\OrderWorkflowFactory;
use App\Payment\Common\GatewayInterface;
use App\Payment\Common\PaymentGatewayRegistryInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[Route('/api/v1/payment', name: 'api_v1_payment_')]
class PaymentController extends AbstractController
{
    public function __construct(
        private readonly PaymentGatewayRegistryInterface $paymentGatewayRegistry,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    #[OA\Get(description: 'Get the available payment gateways.')]
    #[OA\Response(
        response: 200,
        description: 'A list of available payment gateways.',
        content: new Model(type: PaymentGatewayDto::class),
    )]
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

    #[OA\Post(
        description: 'Payment callback handler invoked by the payment gateway.',
    )]
    #[OA\Response(
        response: 200,
        description: 'Successful handled.',
    )]
    #[Route('/{order_uuid}/handler', name: 'callback_handler', methods: 'POST', format: 'json')]
    public function paymentCallbackHandler(
        #[MapEntity(mapping: ['order_uuid' => 'uuid'])] Order $order,
        Request $request,
        OrderWorkflowFactory $orderWorkflowFactory,
    ): Response {
        $paymentGateway = $this->paymentGatewayRegistry->getGatewayById($order->getPaymentGateway());

        $event = $this->eventDispatcher->dispatch(new BeforePaymentCallbackHandlerEvent(
            content: $request->getContent(),
            headers: $request->headers->all(),
            paymentGateway: $paymentGateway,
            order: $order,
        ));

        $serverCallbackHandler = $event->paymentGateway->getServerCallbackHandler();

        $response = $serverCallbackHandler->handleCallback($event->content, $event->headers);

        $workflow = $orderWorkflowFactory->createFromContext($order, response: $response);

        if ($response->isSuccessful()) {
            $workflow->setState(OrderStatus::PAYMENT_RECEIVED);
        } else {
            $workflow->setState(OrderStatus::PAYMENT_FAILED);
        }

        $this->eventDispatcher->dispatch(new AfterPaymentCallbackHandlerEvent($order, $response));

        return new Response();
    }

    #[OA\Get(description: 'Get a payment status of the provided order from the payment gateway.')]
    #[OA\Response(
        response: 200,
        description: 'Order payment status.',
        content: new Model(type: PaymentStatusDto::class),
    )]
    #[OA\Response(
        response: 404,
        description: 'Order not found.',
    )]
    #[Route('/{order_uuid}/status', name: 'status', methods: 'GET', format: 'json')]
    public function getPaymentStatus(
        #[MapEntity(mapping: ['order_uuid' => 'uuid'])] Order $order,
    ): JsonResponse {
        $paymentGateway = $this->paymentGatewayRegistry->getGatewayById($order->getPaymentGateway());

        $paymentStatusRequest = $paymentGateway->getPaymentStatusRequestBuilder()->build($order);

        $statusResponse = $paymentStatusRequest->send();

        $event = $this->eventDispatcher->dispatch(new PaymentStatusEvent($order, $statusResponse));
        $statusResponse = $event->paymentStatusResponse;

        return $this->json(PaymentStatusDto::makeFromPaymentGatewayResponse($paymentGateway->getId(), $statusResponse));
    }
}
