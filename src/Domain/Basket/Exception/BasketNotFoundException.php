<?php

declare(strict_types=1);

namespace Domain\Basket\Exception;

use Domain\Basket\BasketId;
use Domain\Product\ProductId;

class BasketNotFoundException extends \InvalidArgumentException
{
    /**
     * @var BasketId
     */
    private $basketId;

    public function __construct(BasketId $basketId)
    {
        $this->basketId = $basketId;

        parent::__construct(sprintf(
            'Basket "%s" not found!',
            $basketId->toString()
        ));
    }

    public function productId(): BasketId
    {
        return $this->basketId;
    }
}
