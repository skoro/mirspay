<?php

namespace App\Entity;

use App\Repository\PaymentProcessingRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

#[ORM\Entity(repositoryClass: PaymentProcessingRepository::class)]
class PaymentProcessing
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $handler = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $message = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private mixed $request = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private mixed $response = null;

    #[ORM\Column]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne()]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Order $order = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHandler(): ?string
    {
        return $this->handler;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function getRequest(): ?array
    {
        return $this->request;
    }

    public function getResponse(): ?array
    {
        return $this->response;
    }

    public static function create(
        Order $order,
        object $handler,
        object $message,
        ?JsonSerializable $request = null,
        ?JsonSerializable $response = null,
    ): static {
        $self = new static();

        $self->order = $order;
        $self->handler = $handler::class;
        $self->message = $message::class;
        $self->request = $request->jsonSerialize();
        $self->response = $response->jsonSerialize();

        $self->createdAt = new DateTimeImmutable();

        return $self;
    }
}
