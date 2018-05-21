<?php


namespace Domain\Basket;


use Domain\ValueObject;
use Prooph\EventSourcing\AggregateChanged;
use Prooph\EventSourcing\AggregateRoot;

class Basket extends AggregateRoot implements ValueObject
{

    public static function create(
        BasketId $basketId,
        ProductName $name,
        ProductPrice $price
    ): self {
        $self = new self();
        $self->recordThat(ProductWasCreated::withData(
            $productId,
            $name,
            $price
        ));

        return $self;
    }

    protected function aggregateId(): string
    {
        // TODO: Implement aggregateId() method.
    }

    /**
     * Apply given event
     */
    protected function apply(AggregateChanged $event): void
    {
        // TODO: Implement apply() method.
    }

    public function equals(ValueObject $other): bool
    {
        // TODO: Implement equals() method.
    }
}