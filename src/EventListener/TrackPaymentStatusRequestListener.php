<?php

namespace App\EventListener;

use App\Entity\PaymentProcessing;
use App\Event\PaymentStatusEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
final class TrackPaymentStatusRequestListener
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function __invoke(PaymentStatusEvent $event): void
    {
        $paymentProcessing = PaymentProcessing::create(
            order: $event->order,
            request: $event->paymentStatusResponse->getRequest(),
            response: $event->paymentStatusResponse,
        );

        $this->em->persist($paymentProcessing);
        $this->em->flush();
    }
}
