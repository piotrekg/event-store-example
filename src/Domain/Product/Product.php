<?php

declare(strict_types=1);

namespace Domain\Product;

use Domain\Product\Event\ProductWasCreated;
use Prooph\EventSourcing\AggregateChanged;
use Prooph\EventSourcing\AggregateRoot;

class Product extends AggregateRoot
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

    public static function create(
        ProductId $productId,
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

    /**
     * @throws Exception\InvalidProductName
     * @throws Exception\InvalidProductPrice
     */
    protected function whenProductWasCreated(ProductWasCreated $event): void
    {
        $this->productId = $event->productId();
        $this->name = $event->name();
        $this->price = $event->price();
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
