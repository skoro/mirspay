<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\SubscriberRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: SubscriberRepository::class)]
#[ORM\Table(name: 'subscribers')]
#[ORM\Index(columns: ['order_status'])]
class Subscriber
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $uuid;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $hash = null;

    #[ORM\Column(length: 255)]
    private ?string $channelType = null;

    #[ORM\Column(enumType: OrderStatus::class)]
    private ?OrderStatus $orderStatus = null;

    #[ORM\Column(length: 255)]
    private ?string $channelMessage = null;

    #[ORM\Column]
    private array $params = [];

    #[ORM\Column]
    private ?DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->uuid = Uuid::v7();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): Uuid
    {
        return $this->uuid;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function getChannelType(): ?string
    {
        return $this->channelType;
    }

    public function getOrderStatus(): ?OrderStatus
    {
        return $this->orderStatus;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Generates a hash of order status, notification type and parameters.
     *
     * @return non-empty-string
     */
    public function generateHash(): string
    {
        $this->hash = md5($this->channelType . $this->orderStatus->value . serialize($this->params));

        return $this->hash;
    }

    public function setChannelType(string $channelType): void
    {
        $this->channelType = $channelType;
    }

    public function setOrderStatus(OrderStatus $orderStatus): void
    {
        $this->orderStatus = $orderStatus;
    }

    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    public function setCreatedAtNow(): void
    {
        $this->createdAt = new DateTimeImmutable();
    }

    public function getChannelMessage(): ?string
    {
        return $this->channelMessage;
    }

    public function setChannelMessage(string $channelMessage): void
    {
        $this->channelMessage = $channelMessage;
    }
}
