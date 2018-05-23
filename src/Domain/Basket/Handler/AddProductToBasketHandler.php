<?php

declare(strict_types=1);

namespace Domain\Basket\Handler;

use Domain\Basket\BasketRepository;
use Domain\Basket\Command\AddProductToBasket;
use Domain\Basket\Exception\BasketNotFoundException;
use Domain\Basket\Exception\ProductAddedTwiceException;
use Domain\Basket\Exception\ProductNotFoundException;
use Domain\Product\Exception\ProductOutOfStack;
use Domain\Product\ProductRepository;

class AddProductToBasketHandler
{
    /**
     * @var BasketRepository
     */
    private $basketRepository;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    public function __construct(
        BasketRepository $basketRepository,
        ProductRepository $productRepository
    ) {
        $this->basketRepository = $basketRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * @throws ProductNotFoundException
     * @throws ProductOutOfStack
     * @throws ProductAddedTwiceException
     * @throws BasketNotFoundException
     */
    public function __invoke(AddProductToBasket $command): void
    {
        $product = $this->productRepository->get($command->productId());

        if (null === $product) {
            throw new ProductNotFoundException($command->productId());
        }

        if (false === $product->stack()->inStack()) {
            throw ProductOutOfStack::withProductId($product->productId());
        }

        $basket = $this->basketRepository->get($command->basketId());

        if (null === $basket) {
            throw new BasketNotFoundException($command->basketId());
        }

        $basket->addProduct($command->productId());

        $this->basketRepository->save($basket);
    }
}
