<?php

declare(strict_types=1);

namespace Application\Exception;

use Domain\Product\BasketId;

class ProductNotFoundException extends \Exception
{
    /**
     * @var BasketId
     */
    private $productId;

    public function __construct(BasketId $productId)
    {
        $this->productId = $productId;

        parent::__construct(sprintf(
            'Product with id "%s" not found!',
            $productId->toString()
        ));
    }

    public function productId(): BasketId
    {
        return $this->productId;
    }
}
