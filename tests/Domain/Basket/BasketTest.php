<?php
/**
 * Created by PhpStorm.
 * User: piotrek
 * Date: 22/05/2018
 * Time: 14:40
 */

namespace Domain\Basket;

use Domain\Basket\Event\BasketWasCreated;
use Domain\Basket\Event\ProductAddedToBasket;
use Domain\Basket\Exception\BasketIsEmptyException;
use Domain\Basket\Exception\ProductAddedTwiceException;
use Domain\Basket\Exception\ProductNotFoundInBasketException;
use Domain\Product\ProductId;
use PHPUnit\Framework\TestCase;
use Prooph\EventSourcing\EventStoreIntegration\AggregateRootDecorator;

class BasketTest extends TestCase
{
    /**
     * @var AggregateRootDecorator
     */
    private $decorator;

    public function setup()
    {
        $this->decorator = AggregateRootDecorator::newInstance();
    }

    /**
     * @throws \ReflectionException
     * @throws \PHPUnit_Framework_Exception
     */
    public function testCreateBasket(): void
    {
        // given
        $id = BasketId::generate();

        // when
        $basket = Basket::createBasket($id);

        // then
        $this->assertInstanceOf(BasketId::class, $basket->basketId());
        $this->assertEquals($id->toString(), $this->callMethod($basket, 'aggregateId'));
        $this->assertEquals($id, $basket->basketId());

        $recordedEvents = $this->decorator->extractRecordedEvents($basket);
        $this->decorator->replayStreamEvents($basket, new \ArrayIterator($recordedEvents));

        $this->assertEquals(1, count($recordedEvents));

        /** @var BasketWasCreated $basketWasCreated */
        $basketWasCreated = $recordedEvents[0];
        $this->assertEquals($id, $basketWasCreated->basketId());
    }

    /**
     * @throws Exception\ProductAddedTwiceException
     * @throws \ReflectionException
     * @throws \PHPUnit_Framework_Exception
     */
    public function testAddProductSuccess()
    {
        // given
        $productId = ProductId::generate();
        $basketId = BasketId::generate();

        // when
        $basket = Basket::createBasket($basketId);
        $basket->addProduct($productId);

        // then
        $this->assertInstanceOf(BasketId::class, $basket->basketId());
        $this->assertEquals($basketId->toString(), $this->callMethod($basket, 'aggregateId'));
        $this->assertEquals($basketId, $basket->basketId());
        $this->assertEquals(1, $basket->productsCount());

        $recordedEvents = $this->decorator->extractRecordedEvents($basket);
        $this->decorator->replayStreamEvents($basket, new \ArrayIterator($recordedEvents));

        $this->assertEquals(2, count($recordedEvents));

        /** @var ProductAddedToBasket $productAddedToBasket */
        $productAddedToBasket = $recordedEvents[1];
        $this->assertEquals($basketId, $productAddedToBasket->basketId());
        $this->assertEquals($productId, $productAddedToBasket->productId());
    }

    /**
     * @throws Exception\ProductAddedTwiceException
     * @throws \ReflectionException
     * @throws \PHPUnit_Framework_Exception
     */
    public function testAddProductFailWithDoubleProduct()
    {
        $this->expectException(ProductAddedTwiceException::class);

        // given
        $productId = ProductId::generate();
        $basketId = BasketId::generate();
        $basket = Basket::createBasket($basketId);

        // when
        $basket->addProduct($productId);
        $basket->addProduct($productId);

        // then
    }

    /**
     * @throws \ReflectionException
     * @throws Exception\ProductNotFoundInBasketException
     * @throws ProductAddedTwiceException
     * @throws BasketIsEmptyException
     * @throws \PHPUnit_Framework_Exception
     */
    public function testRemoveProduct()
    {
        // given
        $productIdOne = ProductId::generate();
        $productIdTwo = ProductId::generate();
        $basketId = BasketId::generate();

        // when
        $basket = Basket::createBasket($basketId);
        $basket->addProduct($productIdOne);
        $basket->addProduct($productIdTwo);
        $basket->removeProduct($productIdOne);

        // then
        $this->assertInstanceOf(BasketId::class, $basket->basketId());
        $this->assertEquals($basketId->toString(), $this->callMethod($basket, 'aggregateId'));
        $this->assertEquals($basketId, $basket->basketId());
        $this->assertEquals(1, $basket->productsCount());

        $recordedEvents = $this->decorator->extractRecordedEvents($basket);
        $this->decorator->replayStreamEvents($basket, new \ArrayIterator($recordedEvents));

        $this->assertEquals(4, count($recordedEvents));

        /** @var ProductAddedToBasket $productAddedToBasket */
        $productAddedToBasket = $recordedEvents[2];
        $this->assertEquals($basketId, $productAddedToBasket->basketId());
        $this->assertEquals($productIdTwo, $productAddedToBasket->productId());
    }

    /**
     * @throws \ReflectionException
     * @throws ProductAddedTwiceException
     * @throws ProductNotFoundInBasketException
     * @throws Exception\BasketIsEmptyException
     * @throws \PHPUnit_Framework_Exception
     */
    public function testRemoveProductFailWithProductNotFound()
    {
        $this->expectException(ProductNotFoundInBasketException::class);

        // given
        $productId = ProductId::generate();
        $basketId = BasketId::generate();
        $basket = Basket::createBasket($basketId);

        // when
        $basket->addProduct($productId);
        $basket->removeProduct(ProductId::generate());

        // then
    }

    /**
     * @throws \ReflectionException
     * @throws ProductAddedTwiceException
     * @throws ProductNotFoundInBasketException
     * @throws Exception\BasketIsEmptyException
     * @throws \PHPUnit_Framework_Exception
     */
    public function testRemoveProductFailWithEmptyBasket()
    {
        $this->expectException(BasketIsEmptyException::class);

        // given
        $basketId = BasketId::generate();
        $basket = Basket::createBasket($basketId);

        // when
        $basket->removeProduct(ProductId::generate());

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
