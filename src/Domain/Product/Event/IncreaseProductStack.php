<?php

declare(strict_types=1);

namespace Domain\Product\Event;

use Domain\Product\ProductId;
use Domain\Product\ProductName;
use Domain\Product\ProductPrice;
use Domain\Product\ProductStock;
use Prooph\EventSourcing\AggregateChanged;

class IncreaseProductStack extends AggregateChanged
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var ProductStock
     */
    private $oldStock;

    /**
     * @var ProductStock
     */
    private $newStock;

    public static function increase(
        ProductId $productId,
        ProductStock $oldStock,
        ProductStock $newStock
    ): self {
        /** @var self $event */
        $event = self::occur($productId->toString(), [
            'old_stock' => $oldStock->toString(),
            'new_stock' => $newStock->toString()
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
     * @throws \Domain\Product\Exception\InvalidProductStock
     */
    public function oldStock(): ProductStock
    {
        if (null === $this->oldStock) {
            $this->oldStock = ProductStock::fromString($this->payload['old_stock']);
        }

        return $this->oldStock;
    }

    /**
     * @throws \Domain\Product\Exception\InvalidProductStock
     */
    public function newStock(): ProductStock
    {
        if (null === $this->newStock) {
            $this->newStock = ProductStock::fromString($this->payload['new_stock']);
        }

        return $this->newStock;
    }
}
