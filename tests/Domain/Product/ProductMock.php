<?php

declare(strict_types=1);

namespace App\Tests\Domain\Product;

use Domain\Product\Product;
use Domain\Product\ProductId;
use Domain\Product\ProductName;
use Domain\Product\ProductPrice;
use Domain\Product\ProductStack;

class ProductMock
{
    /**
     * @throws \Domain\Product\Exception\InvalidProductName
     * @throws \Domain\Product\Exception\InvalidProductPrice
     * @throws \Domain\Product\Exception\InvalidProductStack
     */
    public static function get(
        string $name = 'Test name 1',
        float $price = 1.23,
        int $stock = 100
    ): Product {
        return Product::create(
            ProductId::generate(),
            ProductName::fromString($name),
            ProductPrice::fromString((string) $price),
            ProductStack::fromString((string) $stock)
        );
    }
}
