<?php

declare(strict_types=1);

namespace Domain\Basket\Event;

use Domain\Basket\BasketId;
use Domain\Product\ProductId;
use Prooph\EventSourcing\AggregateChanged;

class ProductRemovedFromBasket extends AggregateChanged
{
    /**
     * @var BasketId
     */
    private $basketId;

    /**
     * @var ProductId
     */
    private $productId;

    public static function toBasket(BasketId $basketId, ProductId $productId)
    {
        /** @var self $event */
        $event = self::occur($basketId->toString(), [
            'product_id' => $productId,
        ]);

        $event->basketId = $basketId;
        $event->productId = $productId;

        return $event;
    }

    public function basketId(): BasketId
    {
        if (null === $this->basketId) {
            $this->basketId = BasketId::fromString($this->aggregateId());
        }

        return $this->basketId;
    }

    public function productId(): ProductId
    {
        if (null === $this->productId) {
            $this->productId = ProductId::fromString($this->payload['product_id']);
        }

        return $this->productId;
    }
}
