<?php

declare(strict_types=1);

namespace Domain\Product;

use PHPUnit\Framework\TestCase;

class ProductStackTest extends TestCase
{
    /**
     * @throws Exception\InvalidProductStack
     */
    public function testDecreaseStack()
    {
        // given
        $stack = ProductStack::fromString('2');

        // when
        $stack = $stack->decrease();
        $stack = $stack->decrease();

        // then
        $this->assertEquals(0, $stack->get());
    }

    /**
     * @throws Exception\InvalidProductStack
     */
    public function testIncreaseStack()
    {
        // given
        $stack = ProductStack::fromString('2');

        // when
        $stack = $stack->increase();
        $stack = $stack->increase();

        // then
        $this->assertEquals(4, $stack->get());
    }
}
