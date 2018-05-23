<?php

declare(strict_types=1);

namespace Domain\Basket\Handler;

use Domain\Basket\Basket;
use Domain\Basket\BasketRepository;
use Domain\Basket\Command\AddProductToBasket;
use Domain\Basket\Command\CreateNewBasket;
use Domain\Basket\Exception\ProductAddedTwiceException;
use Domain\Product\ProductRepository;

class AddProductToBasketHandler
{
    /**
     * @var BasketRepository
     */
    private $repository;

    public function __construct(BasketRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @throws ProductAddedTwiceException
     */
    public function __invoke(AddProductToBasket $command): void
    {
        $product = $this->repository->get($command->basketId());

        $product->addProduct($command->productId());

        $this->repository->save($product);
    }
}