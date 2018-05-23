<?php

declare(strict_types=1);

namespace Infrastructure\Basket;

use Domain\Basket\Basket;
use Domain\Basket\BasketId;
use Domain\Basket\BasketRepository;
use Prooph\EventSourcing\Aggregate\AggregateRepository;

final class EventStoreBasketRepository extends AggregateRepository implements BasketRepository
{
    public function save(Basket $basket): void
    {
        $this->saveAggregateRoot($basket);
    }

    public function get(BasketId $basketId): ?Basket
    {
        return $this->getAggregateRoot($basketId->toString());
    }
}
