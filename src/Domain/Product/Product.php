<?php

declare(strict_types=1);

namespace Domain\Product;

use Domain\Product\Event\IncreaseProductStack;
use Domain\Product\Event\ProductWasAdded;
use Domain\Product\Event\ProductWasCreated;
use Domain\Product\Event\DecreaseProductStack;
use Domain\Product\Exception\ProductOutOfStack;
use Domain\ValueObject;
use Prooph\EventSourcing\AggregateChanged;
use Prooph\EventSourcing\AggregateRoot;

final class Product extends AggregateRoot implements ValueObject
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var ProductName
     */
    private $name;

    /**
     * @var ProductPrice
     */
    private $price;

    /**
     * @var ProductStack
     */
    private $stack;

    public static function create(
        ProductId $productId,
        ProductName $name,
        ProductPrice $price,
        ProductStack $stack
    ): self {
        $self = new self();
        $self->recordThat(ProductWasCreated::withData(
            $productId,
            $name,
            $price,
            $stack
        ));

        return $self;
    }

    /**
     * @throws ProductOutOfStack
     */
    public function decreaseStack(): void
    {
        if (false === $this->stack()->inStack()) {
            throw ProductOutOfStack::withProductId($this->productId());
        }
        
        $this->recordThat(DecreaseProductStack::decrease(
            $this->productId(),
            $this->stack(),
            $this->stack()->decrease()
        ));
    }

    public function increaseStack(): void
    {
        $this->recordThat(IncreaseProductStack::increase(
            $this->productId(),
            $this->stack(),
            $this->stack()->increase()
        ));
    }

    public function productId(): ProductId
    {
        return $this->productId;
    }

    public function name(): ProductName
    {
        return $this->name;
    }

    public function price(): ProductPrice
    {
        return $this->price;
    }

    public function stack(): ProductStack
    {
        return $this->stack;
    }

    public function equals(ValueObject $other): bool
    {
        return $this->productId()->equals($other->productId())
            && $this->name()->equals($other->name())
            && $this->price()->equals($other->price())
            && $this->stack()->equals($other->stack())
        ;
    }

    /**
     * @throws Exception\InvalidProductName
     * @throws Exception\InvalidProductPrice
     * @throws Exception\InvalidProductStack
     */
    protected function whenProductWasCreated(ProductWasCreated $event): void
    {
        $this->productId = $event->productId();
        $this->name = $event->name();
        $this->price = $event->price();
        $this->stack = $event->stack();
    }

    /**
     * @throws Exception\InvalidProductStack
     */
    protected function whenDecreaseProductStack(DecreaseProductStack $event): void
    {
        $this->stack = $event->newStack();
    }

    /**
     * @throws Exception\InvalidProductStack
     */
    protected function whenIncreaseProductStack(IncreaseProductStack $event): void
    {
        $this->stack = $event->newStack();
    }

    protected function aggregateId(): string
    {
        return $this->productId()->toString();
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
