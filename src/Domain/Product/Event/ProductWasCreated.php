<?php

declare(strict_types=1);

namespace Domain\Product\Event;

use Domain\Product\ProductId;
use Domain\Product\ProductName;
use Domain\Product\ProductPrice;
use Domain\Product\ProductStack;
use Prooph\EventSourcing\AggregateChanged;

class ProductWasCreated extends AggregateChanged
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

    public static function withData(
        ProductId $productId,
        ProductName $name,
        ProductPrice $price,
        ProductStack $stack
    ): self {
        /** @var self $event */
        $event = self::occur($productId->toString(), [
            'name' => $name->toString(),
            'price' => $price->toString(),
            'stack' => $stack->toString(),
        ]);

        $event->productId = $productId;
        $event->name = $name;
        $event->price = $price;
        $event->stack = $stack;

        return $event;
    }

    public function productId(): ProductId
    {
        if (null === $this->productId) {
            $this->productId = ProductId::fromString($this->aggregateId());
        }

        return $this->productId;
    }

    /**
     * @throws \Domain\Product\Exception\InvalidProductName
     */
    public function name(): ProductName
    {
        if (null === $this->name) {
            $this->name = ProductName::fromString($this->payload['name']);
        }

        return $this->name;
    }

    /**
     * @throws \Domain\Product\Exception\InvalidProductPrice
     */
    public function price(): ProductPrice
    {
        if (null === $this->price) {
            $this->price = ProductPrice::fromString($this->payload['price']);
        }

        return $this->price;
    }

    /**
     * @throws \Domain\Product\Exception\InvalidProductStack
     */
    public function stack(): ProductStack
    {
        if (null === $this->stack) {
            $this->stack = ProductStack::fromString($this->payload['stack'] ?? '0');
        }

        return $this->stack;
    }
}
