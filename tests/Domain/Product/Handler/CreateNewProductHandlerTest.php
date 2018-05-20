<?php

declare(strict_types=1);

namespace Domain\Product\Handler;

use App\Tests\Domain\Product\ProductMock;
use Domain\Product\Command\CreateNewProduct;
use Domain\Product\Product;
use Domain\Product\ProductRepository;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

class CreateNewProductHandlerTest extends TestCase
{
    /**
     * @var ObjectProphecy|ProductRepository
     */
    private $repository;

    /**
     * @throws \LogicException
     */
    public function setUp()
    {
        $this->repository = $this->prophesize(ProductRepository::class);
    }

    /**
     * @throws \Domain\Product\Exception\InvalidProductName
     * @throws \Domain\Product\Exception\InvalidProductPrice
     * @throws \Domain\Product\Exception\ProductAlreadyExists
     * @throws \LogicException
     * @throws \Prophecy\Exception\Prophecy\ObjectProphecyException
     * @throws \PHPUnit\Framework\AssertionFailedError
     */
    public function test__invoke()
    {
        // given
        /** @var Product|null $saved */
        $saved = null;
        $product = ProductMock::get();

        $this
            ->repository
            ->get($product->productId())
            ->shouldBeCalledTimes(1)
            ->willReturn(null)
        ;

        $this
            ->repository
            ->save(Argument::any())
            ->shouldBeCalledTimes(1)
            ->will(function ($args) use ($product, &$saved) {
                $saved = $args[0];
            })
        ;

        $handler = new CreateNewProductHandler($this->repository->reveal());

        // when
        $handler(CreateNewProduct::withData(
            $product->productId()->toString(),
            $product->name()->toString(),
            $product->price()->get()
        ));

        // then
        $this->assertTrue($product->equals($saved));
    }
}
