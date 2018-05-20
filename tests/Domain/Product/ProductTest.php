<?php

namespace Domain\Product;

use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{

    /**
     * @throws \PHPUnit\Framework\Exception
     */
    public function testCreate()
    {
        // given
        $name = 'Test product';
        $price = 1.23;

        // when
        $product = Product::create($name, $price);

        // then
        $this->assertInstanceOf(ProductId::class, $product->productId());
        $this->assertEquals($name, $product->name());
        $this->assertEquals($price, $product->price());
    }
}
