<?php

namespace Mirspay\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Mirspay\Payment\Common\Message\RequestInterface;
use Mirspay\Payment\Common\Message\ResponseInterface;
use Mirspay\Repository\PaymentProcessingRepository;

#[ORM\Entity(repositoryClass: PaymentProcessingRepository::class)]
class PaymentProcessing
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $requestName = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private mixed $requestParams = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $responseName = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private mixed $responseData = null;

    #[ORM\Column]
    private ?bool $responseSuccess = null;

    #[ORM\Column]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne()]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Order $order = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function getRequestName(): ?string
    {
        return $this->requestName;
    }

    public function getRequestParams(): mixed
    {
        return $this->requestParams;
    }

    public function getResponseName(): ?string
    {
        return $this->responseName;
    }

    public function getResponseData(): mixed
    {
        return $this->responseData;
    }

    public function getResponseSuccess(): ?bool
    {
        return $this->responseSuccess;
    }

    public static function create(
        Order $order,
        RequestInterface | null $request,
        ResponseInterface | null $response,
    ): static {
        $self = new static();

        $self->order = $order;

        if ($request) {
            $self->requestName = $request::class;
            $self->requestParams = $request->jsonSerialize();
        }

        if ($response) {
            $self->responseName = $response::class;
            $self->responseData = $response->jsonSerialize();
            $self->responseSuccess = $response->isSuccessful();
        }

        $self->createdAt = new DateTimeImmutable();

        return $self;
    }
}
