<?php

declare(strict_types=1);

namespace Domain\Product\Handler;

use App\Tests\Domain\Product\ProductMock;
use Domain\Product\Command\CreateNewProduct;
use Domain\Product\Exception\ProductAlreadyExists;
use Domain\Product\Product;
use Domain\Product\ProductRepository;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class CreateNewProductHandlerTest extends TestCase
{
    /**
     * @throws \Domain\Product\Exception\InvalidProductName
     * @throws \Domain\Product\Exception\InvalidProductPrice
     * @throws \Domain\Product\Exception\ProductAlreadyExists
     * @throws \LogicException
     * @throws \Prophecy\Exception\Prophecy\ObjectProphecyException
     * @throws \PHPUnit\Framework\AssertionFailedError
     */
    public function testHandleSuccess(): void
    {
        // given
        /** @var Product|null $saved */
        $saved = null;
        $product = ProductMock::get();

        $repository = $this->prophesize(ProductRepository::class);

        $repository
            ->get($product->productId())
            ->shouldBeCalledTimes(1)
            ->willReturn(null)
        ;

        $repository
            ->save(Argument::any())
            ->shouldBeCalledTimes(1)
            ->will(function ($args) use ($product, &$saved) {
                $saved = $args[0];
            })
        ;

        $handler = new CreateNewProductHandler($repository->reveal());

        // when
        $handler(CreateNewProduct::withData(
            $product->productId()->toString(),
            $product->name()->toString(),
            $product->price()->get()
        ));

        // then
        $this->assertTrue($product->equals($saved));
    }

    /**
     * @throws \Domain\Product\Exception\InvalidProductName
     * @throws \Domain\Product\Exception\InvalidProductPrice
     * @throws \Domain\Product\Exception\ProductAlreadyExists
     * @throws \LogicException
     * @throws \Prophecy\Exception\Prophecy\ObjectProphecyException
     * @throws \PHPUnit\Framework\AssertionFailedError
     */
    public function testHandleFail(): void
    {
        $this->expectException(ProductAlreadyExists::class);

        // given
        $product = ProductMock::get();

        $repository = $this->prophesize(ProductRepository::class);

        $repository
            ->get($product->productId())
            ->shouldBeCalledTimes(1)
            ->willReturn($product)
        ;

        $repository
            ->save(Argument::any())
            ->shouldNotBeCalled()
        ;

        $handler = new CreateNewProductHandler($repository->reveal());

        // when
        $handler(CreateNewProduct::withData(
            $product->productId()->toString(),
            $product->name()->toString(),
            $product->price()->get()
        ));

        // then
    }
}
