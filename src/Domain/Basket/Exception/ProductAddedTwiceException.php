<?php


namespace Domain\Basket\Exception;


use Domain\Basket\BasketId;
use Domain\Product\ProductId;

class ProductAddedTwiceException extends \InvalidArgumentException
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
            'Product "%s" added to basket "%s" twice!',
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