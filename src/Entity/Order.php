<?php

declare(strict_types=1);

namespace App\Entity;

use App\Dto\OrderDto;
use App\Order\OrderTotalAmountCalculator;
use App\Repository\OrderRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Money\Currency;
use Money\Money;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: 'orders')]
#[ORM\UniqueConstraint(name: 'external_order_payment', columns: ['external_order_id', 'payment_gateway'])]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $externalOrderId = null;

    #[ORM\Column(type: 'uuid', unique: true)]
    private ?Uuid $uuid = null;

    #[ORM\Column(length: 16)]
    private ?string $paymentGateway = null;

    #[ORM\Column]
    #[Assert\Positive]
    private ?int $amount = null;

    #[ORM\Column(length: 8)]
    #[Assert\Currency]
    private ?string $currency = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $returnUrl = null;

    #[ORM\Column(length: 16)]
    private ?OrderStatus $status = null;

    #[ORM\Column]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, OrderProduct>
     */
    #[ORM\OneToMany(targetEntity: OrderProduct::class, mappedBy: 'order', orphanRemoval: true)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'cascade')]
    private Collection $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->uuid = Uuid::v7();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getExternalOrderId(): ?string
    {
        return $this->externalOrderId;
    }

    public function setExternalOrderId(string $externalOrderId): static
    {
        $this->externalOrderId = $externalOrderId;

        return $this;
    }

    public function getUuid(): ?Uuid
    {
        return $this->uuid;
    }

    public function setAmount(Money $money): static
    {
        $this->amount = (int) $money->getAmount();
        $this->currency = $money->getCurrency()->getCode();

        return $this;
    }

    public function getAmount(): Money
    {
        return new Money($this->amount, new Currency($this->currency));
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStatus(): ?OrderStatus
    {
        return $this->status;
    }

    public function setStatus(OrderStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getReturnUrl(): ?string
    {
        return $this->returnUrl;
    }

    public function setReturnUrl(?string $returnUrl): static
    {
        $this->returnUrl = $returnUrl;

        return $this;
    }

    public static function createFromOrderDto(
        OrderDto                   $orderDto,
        OrderTotalAmountCalculator $orderAmountCalculator,
    ): static {
        $self = new static();

        $amount = $orderAmountCalculator->calcTotalOfProductDto($orderDto->products);
        $self->setAmount($amount);

        $self->externalOrderId = $orderDto->orderNum;
        $self->description = $orderDto->description;
        $self->returnUrl = $orderDto->returnUrl;
        $self->paymentGateway = $orderDto->paymentGateway;

        $self->createdAt = new DateTimeImmutable();
        $self->updatedAt = new DateTimeImmutable();

        $self->status = OrderStatus::CREATED;

        return $self;
    }

    public function getPaymentGateway(): ?string
    {
        return $this->paymentGateway;
    }

    public function setPaymentGateway(string $payment_gateway): static
    {
        $this->paymentGateway = $payment_gateway;

        return $this;
    }

    /**
     * @return Collection<int, OrderProduct>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(OrderProduct $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setOrder($this);
        }

        return $this;
    }

    public function removeProduct(OrderProduct $product): static
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getOrder() === $this) {
                $product->setOrder(null);
            }
        }

        return $this;
    }
}
