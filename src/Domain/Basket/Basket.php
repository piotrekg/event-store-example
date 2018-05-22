<?php

declare(strict_types=1);

namespace Domain\Basket;

use function array_keys;
use Domain\Basket\Event\BasketWasCreated;
use Domain\Basket\Event\ProductAddedToBasket;
use Domain\Basket\Event\ProductRemovedFromBasket;
use Domain\Basket\Exception\BasketIsEmptyException;
use Domain\Basket\Exception\ProductAddedTwiceException;
use Domain\Basket\Exception\ProductNotFoundInBasketException;
use Domain\Product\ProductId;
use Domain\ValueObject;
use function key;
use function key_exists;
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

    protected function __construct()
    {
        $this->products = [];
    }

    public static function createBasket(BasketId $basketId): self
    {
        $self = new self();
        $self->recordThat(BasketWasCreated::create($basketId));

        return $self;
    }

    /**
     * @throws ProductAddedTwiceException
     */
    public function addProduct(ProductId $productId)
    {
        if (key_exists($productId->toString(), $this->products)) {
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
     * @throws BasketIsEmptyException
     */
    public function removeProduct(ProductId $productId)
    {
        if ($this->isEmptyBasket()) {
            throw new BasketIsEmptyException($this->basketId());
        }

        if (!key_exists($productId->toString(), $this->products)) {
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

    public function productsCount(): int
    {
        return count($this->products);
    }

    private function isEmptyBasket(): bool
    {
        return 0 === $this->productsCount();
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

    protected function whenBasketWasCreated(BasketWasCreated $event): void
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
