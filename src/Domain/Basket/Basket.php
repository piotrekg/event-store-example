<?php

declare(strict_types=1);

namespace Domain\Basket;

use Domain\Basket\Event\BasketCreated;
use Domain\ValueObject;
use Prooph\EventSourcing\AggregateChanged;
use Prooph\EventSourcing\AggregateRoot;

class Basket extends AggregateRoot implements ValueObject
{
    /**
     * @var BasketId
     */
    private $basketId;

    public static function createBasket(BasketId $basketId)
    {
        $self = new self();
        $self->recordThat(BasketCreated::create($basketId));

        return $self;
    }

    public function basketId(): BasketId
    {
        return $this->basketId;
    }

    protected function aggregateId(): string
    {
        return $this->basketId();
    }

    public function equals(ValueObject $other): bool
    {
        $this->basketId()->equals($other->basketId());
    }

    protected function whenBasketCreated(BasketCreated $event): void
    {
        $this->basketId = $event->basketId();
    }

    /**
     * @throws \RuntimeException
     */
    protected function apply(AggregateChanged $e): void
    {
        $handler = $this->determineEventHandlerMethodFor($e);
        if (!method_exists($this, $handler)) {
            throw new \RuntimeException(sprintf(
                'Missing event handler method %s for aggregate root %s',
                $handler,
                get_class($this)
            ));
        }
        $this->{$handler}($e);
    }

    protected function determineEventHandlerMethodFor(AggregateChanged $e): string
    {
        return 'when'.implode(array_slice(explode('\\', get_class($e)), -1));
    }
}
