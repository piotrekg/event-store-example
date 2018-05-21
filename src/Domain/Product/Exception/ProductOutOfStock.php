<?php

declare(strict_types=1);

namespace Domain\Product\Exception;

use Domain\Product\ProductId;

final class ProductOutOfStock extends \InvalidArgumentException
{
    public static function withProductId(ProductId $productId): self
    {
        return new self(sprintf(
            'Product "%s" is out of stock!',
            $productId->toString()
        ));
    }
}
