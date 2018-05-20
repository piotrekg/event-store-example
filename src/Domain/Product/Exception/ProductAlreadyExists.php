<?php

declare(strict_types=1);

namespace Domain\Product\Exception;

use Domain\Product\ProductId;

class ProductAlreadyExists extends \InvalidArgumentException
{
    public static function withProductId(ProductId $productId): self
    {
        return new self(sprintf(
            'Product with id "%s" allready exists!',
            $productId->toString()
        ));
    }
}
