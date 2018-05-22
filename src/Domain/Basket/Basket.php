<?php

declare(strict_types=1);

namespace Domain\Basket;

use Domain\Basket\Event\BasketCreated;
use Domain\Basket\Event\ProductAddedToBasket;
use Domain\Basket\Event\ProductRemovedFromBasket;
use Domain\Basket\Exception\ProductAddedTwiceException;
use Domain\Basket\Exception\ProductNotFoundInBasketException;
use Domain\Product\ProductId;
use Domain\ValueObject;
use Prooph\EventSourcing\AggregateChanged;
use Prooph\EventSourcing\AggregateRoot;

class Basket extends AggregateRoot implements ValueObject
{
    /**
     * @var BasketId
     */
    private $basketId;

    /**
     * @var ProductId[]
     */
    private $products;

    public static function createBasket(BasketId $basketId): self
    {
        $self = new self();
        $self->recordThat(BasketCreated::create($basketId));

        return $self;
    }

    /**
     * @throws ProductAddedTwiceException
     */
    public function addProduct(ProductId $productId)
    {
        if (in_array($productId->toString(), $this->products)) {
            throw new ProductAddedTwiceException(
                $this->basketId(),
                $productId
            );
        }

        $this->recordThat(ProductAddedToBasket::toBasket(
            $this->basketId(),
            $productId
        ));
    }

    /**
     * @throws ProductNotFoundInBasketException
     */
    public function removeProduct(ProductId $productId)
    {
        if (in_array($productId->toString(), $this->products)) {
            throw new ProductNotFoundInBasketException(
                $this->basketId(),
                $productId
            );
        }

        $this->recordThat(ProductRemovedFromBasket::toBasket(
            $this->basketId(),
            $productId
        ));
    }

    public function basketId(): BasketId
    {
        return $this->basketId;
    }

    protected function aggregateId(): string
    {
        return $this->basketId()->toString();
    }

    public function equals(ValueObject $other): bool
    {
        /* @var self $other */
        $this->basketId()->equals($other->basketId());
    }

    protected function whenBasketCreated(BasketCreated $event): void
    {
        $this->basketId = $event->basketId();
    }

    protected function whenProductAddedToBasket(ProductAddedToBasket $event): void
    {
        $this->products[$event->productId()->toString()] = $event->productId();
    }

    protected function whenProductRemovedFromBasket(ProductRemovedFromBasket $event): void
    {
        unset($this->products[$event->productId()->toString()]);
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
