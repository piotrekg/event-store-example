<?php

declare(strict_types=1);

namespace Domain\Basket\Command;

use Assert\Assertion;
use Domain\Basket\BasketId;
use Domain\Product\ProductId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class AddProductToBasket extends Command implements PayloadConstructable
{
    use PayloadTrait;

    public static function withData(string $basketId, string $productId): self
    {
        return new self([
            'basket_id' => $basketId,
            'product_id' => $productId,
        ]);
    }

    public function basketId(): BasketId
    {
        return BasketId::fromString($this->payload['basket_id']);
    }

    public function productId(): ProductId
    {
        return ProductId::fromString($this->payload['product_id']);
    }

    /**
     * @throws \Assert\AssertionFailedException
     */
    protected function setPayload(array $payload): void
    {
        Assertion::keyExists($payload, 'basket_id');
        Assertion::uuid($payload['basket_id']);

        Assertion::keyExists($payload, 'product_id');
        Assertion::uuid($payload['product_id']);

        $this->payload = $payload;
    }
}
