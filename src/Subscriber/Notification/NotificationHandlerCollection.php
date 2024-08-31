<?php

declare(strict_types=1);

namespace App\Subscriber\Notification;

use App\Subscriber\Exception\NotificationHandlerNotRegisteredException;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class NotificationHandlerCollection
{
    /**
     * @var array<string, NotificationHandlerInterface>
     */
    private readonly array $handlers;

    public function __construct(
        #[TaggedIterator('app.subscriber.notification', indexAttribute: 'type')]
        iterable $handlers,
    ) {
        $this->handlers = iterator_to_array($handlers);
    }

    /**
     * @param non-empty-string $type
     * @throws NotificationHandlerNotRegisteredException
     */
    public function getHandler(string $type): NotificationHandlerInterface
    {
        return $this->handlers[$type]
            ?? throw new NotificationHandlerNotRegisteredException($type);
    }

    /**
     * @return string[]
     */
    public function getHandlerTypes(): array
    {
        return array_keys($this->handlers);
    }
}
