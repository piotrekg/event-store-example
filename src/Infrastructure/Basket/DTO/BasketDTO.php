<?php

declare(strict_types=1);

namespace Infrastructure\Basket\DTO;

use Domain\Basket\BasketId;
use Domain\Product\ProductId;

class BasketDTO
{
    /**
     * @var BasketId
     */
    private $basketId;

    /**
     * @var ProductId[]
     */
    private $products;

    public function __construct(BasketId $basketId, array $products)
    {
        $this->basketId = $basketId;

        foreach ($products as $product) {
            $this->addProduct($product);
        }
    }

    private function addProduct(ProductId $productId): void
    {
        $this->products[] = $productId->toString();
    }

    public function getBasketId(): BasketId
    {
        return $this->basketId;
    }

    public function getProducts(): array
    {
        return $this->products;
    }
}
