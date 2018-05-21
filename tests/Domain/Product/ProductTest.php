<?php

declare(strict_types=1);

namespace Domain\Product;

use App\Tests\Domain\Product\ProductMock;
use Domain\Product\Event\DecreaseProductStack;
use Domain\Product\Event\ProductWasCreated;
use Domain\Product\Exception\InvalidProductName;
use Domain\Product\Exception\InvalidProductPrice;
use Domain\Product\Exception\InvalidProductStock;
use Domain\Product\Exception\ProductOutOfStock;
use PHPUnit\Framework\TestCase;
use Prooph\EventSourcing\EventStoreIntegration\AggregateRootDecorator;

class ProductTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \LogicException
     * @throws \ReflectionException
     * @throws \PHPUnit_Framework_Exception
     */
    public function testCreate(): void
    {
        $decorator = AggregateRootDecorator::newInstance();

        // given
        $id = ProductId::generate();
        $name = ProductName::fromString('Test product');
        $price = ProductPrice::fromString('1.23');
        $stock = ProductStock::fromString('100');

        // when
        $product = Product::create($id, $name, $price, $stock);

        // then
        $this->assertInstanceOf(ProductId::class, $product->productId());
        $this->assertEquals($id->toString(), $this->callMethod($product, 'aggregateId'));
        $this->assertEquals($id, $product->productId());
        $this->assertEquals($name, $product->name());
        $this->assertEquals($price, $product->price());
        $this->assertEquals($stock, $product->stock());

        $recordedEvents = $decorator->extractRecordedEvents($product);
        $decorator->replayStreamEvents($product, new \ArrayIterator($recordedEvents));

        $this->assertEquals(1, count($recordedEvents));

        /** @var ProductWasCreated $productCreatedEvent */
        $productCreatedEvent = $recordedEvents[0];
        $this->assertEquals($id, $productCreatedEvent->productId());
        $this->assertEquals($name, $productCreatedEvent->name());
        $this->assertEquals($price, $productCreatedEvent->price());
        $this->assertEquals($stock, $productCreatedEvent->stock());
        $this->assertEquals(1, $productCreatedEvent->version());
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \LogicException
     * @throws \PHPUnit_Framework_Exception
     */
    public function testCreateWithBrokenPrice(): void
    {
        $this->expectException(InvalidProductPrice::class);

        // given
        $id = ProductId::generate();
        $name = ProductName::fromString('Test product');
        $price = ProductPrice::fromString('aaaa');
        $stock = ProductStock::fromString('100');

        // when
        $product = Product::create($id, $name, $price, $stock);

        // then
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \LogicException
     * @throws \PHPUnit_Framework_Exception
     */
    public function testCreateWithEmptyName(): void
    {
        $this->expectException(InvalidProductName::class);

        // given
        $id = ProductId::generate();
        $name = ProductName::fromString('');
        $price = ProductPrice::fromString('1.23');
        $stock = ProductStock::fromString('100');

        // when
        $product = Product::create($id, $name, $price, $stock);

        // then
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \LogicException
     * @throws \PHPUnit_Framework_Exception
     */
    public function testCreateWithInvalidStock(): void
    {
        $this->expectException(InvalidProductStock::class);

        // given
        $id = ProductId::generate();
        $name = ProductName::fromString('Test name');
        $price = ProductPrice::fromString('1.23');
        $stock = ProductStock::fromString('-1');

        // when
        $product = Product::create($id, $name, $price, $stock);

        // then
    }

    /**
     * @throws InvalidProductName
     * @throws InvalidProductPrice
     * @throws InvalidProductStock
     * @throws \ReflectionException
     * @throws Exception\ProductOutOfStock
     */
    public function testTakeOffFromStock(): void
    {
        $decorator = AggregateRootDecorator::newInstance();

        // given
        $stock = ProductStock::fromString('100');
        $product = ProductMock::get('Test Name', 1.23, $stock->get());

        // when
        $product->decreaseStack();

        // then
        $recordedEvents = $decorator->extractRecordedEvents($product);
        $decorator->replayStreamEvents($product, new \ArrayIterator($recordedEvents));

        $this->assertEquals(2, count($recordedEvents));
        $this->assertEquals(
            $stock->decrease(),
            $product->stock()
        );

        /** @var DecreaseProductStack $productHasBeenTakenOffEvent */
        $productHasBeenTakenOffEvent = $recordedEvents[1];
        $this->assertEquals($product->productId(), $productHasBeenTakenOffEvent->productId());
        $this->assertEquals(
            $stock,
            $productHasBeenTakenOffEvent->oldStock()
        );
        $this->assertEquals(
            $stock->decrease(),
            $productHasBeenTakenOffEvent->newStock()
        );
        $this->assertEquals(2, $productHasBeenTakenOffEvent->version());
    }

    /**
     * @throws InvalidProductName
     * @throws InvalidProductPrice
     * @throws InvalidProductStock
     * @throws \ReflectionException
     * @throws Exception\ProductOutOfStock
     */
    public function testTakeOffFromStockWhenItsEmpty(): void
    {
        $this->expectException(ProductOutOfStock::class);

        // given
        $stock = ProductStock::fromString('0');
        $product = ProductMock::get('Test Name', 1.23, $stock->get());

        // when
        $product->decreaseStack();

        // then
    }

    /**
     * @throws InvalidProductName
     * @throws InvalidProductPrice
     * @throws InvalidProductStock
     * @throws \ReflectionException
     * @throws Exception\ProductOutOfStock
     */
    public function testAddToStock(): void
    {
        $decorator = AggregateRootDecorator::newInstance();

        // given
        $stock = ProductStock::fromString('100');
        $product = ProductMock::get('Test Name', 1.23, $stock->get());

        // when
        $product->increaseStack();

        // then
        $recordedEvents = $decorator->extractRecordedEvents($product);
        $decorator->replayStreamEvents($product, new \ArrayIterator($recordedEvents));

        $this->assertEquals(2, count($recordedEvents));
        $this->assertEquals(
            $stock->increase(),
            $product->stock()
        );

        /** @var DecreaseProductStack $productHasBeenTakenOffEvent */
        $productHasBeenTakenOffEvent = $recordedEvents[1];
        $this->assertEquals($product->productId(), $productHasBeenTakenOffEvent->productId());
        $this->assertEquals(
            $stock,
            $productHasBeenTakenOffEvent->oldStock()
        );
        $this->assertEquals(
            $stock->increase(),
            $productHasBeenTakenOffEvent->newStock()
        );
        $this->assertEquals(2, $productHasBeenTakenOffEvent->version());
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
