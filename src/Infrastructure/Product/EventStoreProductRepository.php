<?php

declare(strict_types=1);

namespace Infrastructure\Product;

use Domain\Product\Product;
use Domain\Product\ProductId;
use Domain\Product\ProductRepository;
use Prooph\EventSourcing\Aggregate\AggregateRepository;

final class EventStoreProductRepository extends AggregateRepository implements ProductRepository
{
    public function save(Product $product): void
    {
        $this->saveAggregateRoot($product);
    }

    public function get(ProductId $productId): ?Product
    {
        return $this->getAggregateRoot($productId->toString());
    }
}
