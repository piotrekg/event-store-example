<?php

declare(strict_types=1);

namespace Domain\Basket\Exception;

use Domain\Product\ProductId;

class ProductNotFoundException extends \InvalidArgumentException
{
    /**
     * @var ProductId
     */
    private $productId;

    public function __construct(ProductId $productId)
    {
        $this->productId = $productId;

        parent::__construct(sprintf(
            'Product "%s" not found!',
            $productId->toString()
        ));
    }

    public function productId(): ProductId
    {
        return $this->productId;
    }
}
