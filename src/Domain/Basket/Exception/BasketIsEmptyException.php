<?php

declare(strict_types=1);

namespace Domain\Basket\Exception;

use Domain\Basket\BasketId;

class BasketIsEmptyException extends \InvalidArgumentException
{
    /**
     * @var BasketId
     */
    private $basketId;

    public function __construct(BasketId $basketId)
    {
        $this->basketId = $basketId;

        parent::__construct(sprintf(
            'Basket "%s" is empty!',
            $basketId->toString()
        ));
    }

    public function basketId(): BasketId
    {
        return $this->basketId;
    }
}
