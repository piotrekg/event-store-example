<?php

declare(strict_types=1);

namespace Domain\Product;

use Domain\Product\Event\ProductWasCreated;
use Domain\Product\Exception\InvalidProductPrice;
use PHPUnit\Framework\TestCase;
use Prooph\EventSourcing\EventStoreIntegration\AggregateRootDecorator;

class ProductTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \LogicException
     */
    public function testCreate()
    {
        $decorator = AggregateRootDecorator::newInstance();

        // given
        $id = ProductId::generate();
        $name = ProductName::fromString('Test product');
        $price = ProductPrice::fromFloat(1.23);

        // when
        $product = Product::create($id, $name, $price);

        // then
        $this->assertInstanceOf(ProductId::class, $product->productId());
        $this->assertEquals($id, $product->productId());
        $this->assertEquals($name, $product->name());
        $this->assertEquals($price, $product->price());

        $recordedEvents = $decorator->extractRecordedEvents($product);
        $decorator->replayStreamEvents($product, new \ArrayIterator($recordedEvents));

        $this->assertEquals(1, count($recordedEvents));

        /** @var ProductWasCreated $productCreatedEvent */
        $productCreatedEvent = $recordedEvents[0];
        $this->assertEquals($id, $productCreatedEvent->productId());
        $this->assertEquals($name, $productCreatedEvent->name());
        $this->assertEquals($price, $productCreatedEvent->price());
        $this->assertEquals(1, $productCreatedEvent->version());
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \LogicException
     */
    public function testCreateWithBrokenPrice()
    {
        $this->expectException(InvalidProductPrice::class);

        // given
        $id = ProductId::generate();
        $name = ProductName::fromString('Test product');
        $price = ProductPrice::fromString('aaaa');

        // when
        $product = Product::create($id, $name, $price);

        // then
    }
}
