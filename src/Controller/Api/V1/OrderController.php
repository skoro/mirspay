<?php

declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Dto\OrderDto;
use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Entity\OrderStatus;
use App\Order\OrderTotalAmountCalculator;
use App\Order\Workflow\OrderWorkflowFactory;
use App\Payment\Common\Exception\PaymentGatewayIsNotRegisteredException;
use App\Payment\PaymentGatewayRegistryInterface;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/api/v1/order', name: 'api_v1_order_')]
class OrderController extends AbstractController
{
    #[OA\Post(
        description: 'Purchase order.',
    )]
    #[OA\Response(
        response: 201,
        description: 'Order is added to the service and waiting for payment gateway callback.',
    )]
    #[OA\Response(
        response: 409,
        description: 'Order with same order number and payment gateway has been already added.',
    )]
    #[OA\Response(
        response: 400,
        description: 'Specified payment gateway is not available.',
    )]
    #[Route('', name: 'purchase', methods: 'POST', format: 'json')]
    public function purchaseOrder(
        #[MapRequestPayload] OrderDto   $orderDto,
        OrderTotalAmountCalculator      $orderAmountCalculator,
        EntityManagerInterface          $entityManager,
        OrderRepository                 $orderRepository,
        PaymentGatewayRegistryInterface $paymentGatewayRegistry,
        OrderWorkflowFactory            $orderWorkflowFactory,
    ): JsonResponse {
        // Cannot accept a new order with existent external order id and payment gateway.
        $existOrder = $orderRepository->findByExternalOrderIdAndPaymentGateway($orderDto->orderNum, $orderDto->paymentGateway);
        if ($existOrder) {
            throw new HttpException(JsonResponse::HTTP_CONFLICT, 'An order with specified order num and payment gateway already exists.');
        }

        // The payment gateway must be registered.
        try {
            $paymentGateway = $paymentGatewayRegistry->getGatewayById($orderDto->paymentGateway);
        } catch (PaymentGatewayIsNotRegisteredException) {
            throw new HttpException(JsonResponse::HTTP_BAD_REQUEST, "Payment gateway '{$orderDto->paymentGateway}' is not available.");
        }

        $order = Order::createFromOrderDto($orderDto, $orderAmountCalculator);
        $entityManager->persist($order);

        foreach ($orderDto->products as $product) {
            $product = OrderProduct::createFromProductDto($product);
            $product->setOrder($order);
            $entityManager->persist($product);
        }

        $entityManager->flush();

        $purchaseRequest = $paymentGateway->getPurchaseRequestBuilder()->build($order);

        $purchaseRequest->setCallbackUrl($this->generateUrl('api_v1_payment_callback_handler', [
            'order_uuid' => $order->getUuid(),
        ], referenceType: UrlGeneratorInterface::ABSOLUTE_URL));

        $paymentRedirectResponse = $purchaseRequest->send();

        $orderWorkflowFactory
            ->createFromContext($order, $purchaseRequest, $paymentRedirectResponse)
            ->setState(OrderStatus::PAYMENT_PENDING);

        return $this->json([
            'order' => $order->getUuid(),
            'status_check' => $this->generateUrl('api_v1_order_status', [
                'order_uuid' => $order->getUuid(),
            ], referenceType: UrlGeneratorInterface::ABSOLUTE_URL),
            'payment_redirect_url' => $paymentRedirectResponse->getRedirectUrl(),
        ], status: Response::HTTP_CREATED);
    }

    #[OA\Get(
        description: 'Get order status.',
    )]
    #[OA\Response(
        response: 200,
        description: 'Order status.',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'order_status',
                    description: 'Order status, one of "created", "payment_pending", "payment_received", "payment_failed"',
                    type: 'string',
                    example: '"payment_pending"',
                ),
            ],
            type: 'object',
        ),
    )]
    #[OA\Response(
        response: 404,
        description: 'Order not found.',
    )]
    #[Route('/{order_uuid}/status', name: 'status', methods: 'GET', format: 'json')]
    public function getOrderStatus(
        #[MapEntity(mapping: ['order_uuid' => 'uuid'])] Order $order,
    ): JsonResponse {
        return $this->json([
            'order_status' => $order->getStatus(),
        ]);
    }
}
