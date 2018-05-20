<?php

declare(strict_types=1);

namespace App\Tests\Domain\Product;

use Domain\Product\Product;
use Domain\Product\ProductId;
use Domain\Product\ProductName;
use Domain\Product\ProductPrice;

class ProductMock
{
    /**
     * @throws \Domain\Product\Exception\InvalidProductName
     * @throws \Domain\Product\Exception\InvalidProductPrice
     */
    public static function get(
        string $name = 'Test name 1',
        float $price = 1.23
    ): Product {
        return Product::create(
            ProductId::generate(),
            ProductName::fromString($name),
            ProductPrice::fromFloat($price)
        );
    }
}
