<?php

declare(strict_types=1);

namespace Domain\Product;

use PHPUnit\Framework\TestCase;

class ProductStockTest extends TestCase
{
    /**
     * @throws Exception\InvalidProductStock
     */
    public function testDecreaseStock()
    {
        // given
        $stock = ProductStock::fromString('2');

        // when
        $stock = $stock->decrease();
        $stock = $stock->decrease();

        // then
        $this->assertEquals(0, $stock->get());
    }

    /**
     * @throws Exception\InvalidProductStock
     */
    public function testIncreaseStock()
    {
        // given
        $stock = ProductStock::fromString('2');

        // when
        $stock = $stock->increase();
        $stock = $stock->increase();

        // then
        $this->assertEquals(4, $stock->get());
    }
}
