<?php

declare(strict_types=1);

namespace Domain\Product;

interface ProductRepository
{
    public function get(ProductId $productId): ?Product;

    public function save(Product $product): void;
}
