<?php

declare(strict_types=1);

namespace Domain\Basket\Exception;

use Domain\Basket\BasketId;
use Domain\Product\ProductId;

class ProductNotFoundInBasketException extends \InvalidArgumentException
{
    /**
     * @var BasketId
     */
    private $basketId;

    /**
     * @var ProductId
     */
    private $productId;

    public function __construct(BasketId $basketId, ProductId $productId)
    {
        $this->basketId = $basketId;
        $this->productId = $productId;

        parent::__construct(sprintf(
            'Product "%s" not found in basket "%s"!',
            $productId->toString(),
            $basketId->toString()
        ));
    }

    public function basketId(): BasketId
    {
        return $this->basketId;
    }

    public function productId(): ProductId
    {
        return $this->productId;
    }
}
