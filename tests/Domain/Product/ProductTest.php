<?php

declare(strict_types=1);

namespace Domain\Product;

use Domain\Product\Event\ProductWasCreated;
use Domain\Product\Exception\InvalidProductName;
use Domain\Product\Exception\InvalidProductPrice;
use PHPUnit\Framework\TestCase;
use Prooph\EventSourcing\EventStoreIntegration\AggregateRootDecorator;

class ProductTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \LogicException
     * @throws \ReflectionException
     */
    public function testCreate(): void
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
        $this->assertEquals($id->toString(), $this->callMethod($product, 'aggregateId'));
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
    public function testCreateWithBrokenPrice(): void
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

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \LogicException
     */
    public function testCreateWithEmptyName(): void
    {
        $this->expectException(InvalidProductName::class);

        // given
        $id = ProductId::generate();
        $name = ProductName::fromString('');
        $price = ProductPrice::fromString(1.23);

        // when
        $product = Product::create($id, $name, $price);

        // then
    }

    /**
     * @throws \ReflectionException
     */
    private static function callMethod($obj, $name, array $args = [])
    {
        $class = new \ReflectionClass($obj);
        $method = $class->getMethod($name);
        $method->setAccessible(true);

        return $method->invokeArgs($obj, $args);
    }
}
