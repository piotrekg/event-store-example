<?php

declare(strict_types=1);

namespace Domain\Product\Event;

use Domain\Product\ProductId;
use Domain\Product\ProductName;
use Domain\Product\ProductPrice;
use Domain\Product\ProductStack;
use Prooph\EventSourcing\AggregateChanged;

class IncreaseProductStack extends AggregateChanged
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var ProductStack
     */
    private $oldStack;

    /**
     * @var ProductStack
     */
    private $newStack;

    public static function increase(
        ProductId $productId,
        ProductStack $oldStack,
        ProductStack $newStack
    ): self {
        /** @var self $event */
        $event = self::occur($productId->toString(), [
            'old_stack' => $oldStack->toString(),
            'new_stack' => $newStack->toString()
        ]);

        $event->productId = $productId;

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
     * @throws \Domain\Product\Exception\InvalidProductStack
     */
    public function oldStack(): ProductStack
    {
        if (null === $this->oldStack) {
            $this->oldStack = ProductStack::fromString($this->payload['old_stack']);
        }

        return $this->oldStack;
    }

    /**
     * @throws \Domain\Product\Exception\InvalidProductStack
     */
    public function newStack(): ProductStack
    {
        if (null === $this->newStack) {
            $this->newStack = ProductStack::fromString($this->payload['new_stack']);
        }

        return $this->newStack;
    }
}
