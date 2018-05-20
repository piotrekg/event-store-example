<?php

declare(strict_types=1);

namespace Domain\Product\Handler;

use Domain\Product\Command\CreateNewProduct;
use Domain\Product\Exception\ProductAlreadyExists;
use Domain\Product\Product;
use Domain\Product\ProductRepository;

class CreateNewProductHandler
{
    /**
     * @var ProductRepository
     */
    private $repository;

    public function __construct(ProductRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @throws \Domain\Product\Exception\InvalidProductName
     * @throws \Domain\Product\Exception\InvalidProductPrice
     * @throws ProductAlreadyExists
     */
    public function __invoke(CreateNewProduct $command): void
    {
        if ($product = $this->repository->get($command->productId())) {
            throw ProductAlreadyExists::withProductId($command->productId());
        }

        $product = Product::create(
            $command->productId(),
            $command->name(),
            $command->price()
        );

        $this->repository->save($product);
    }
}
