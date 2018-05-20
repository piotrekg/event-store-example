<?php

declare(strict_types=1);

namespace Application\Exception;

use Domain\Product\ProductId;

class ProductNotFoundException extends \Exception
{
    /**
     * @var ProductId
     */
    private $productId;

    public function __construct(ProductId $productId)
    {
        $this->productId = $productId;

        parent::__construct(sprintf(
            'Product with id "%s" not found!',
            $productId->toString()
        ));
    }

    public function productId(): ProductId
    {
        return $this->productId;
    }
}
