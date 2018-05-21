<?php

declare(strict_types=1);

namespace Domain\Product\Command;

use Assert\Assertion;
use Domain\Product\ProductId;
use Domain\Product\ProductName;
use Domain\Product\ProductPrice;
use Domain\Product\ProductStack;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class CreateNewProduct extends Command implements PayloadConstructable
{
    use PayloadTrait;

    public static function withData(
        string $productId,
        string $name,
        string $price,
        string $stack
    ): self {
        return new self([
            'product_id' => $productId,
            'name' => $name,
            'price' => $price,
            'stack' => $stack,
        ]);
    }

    public function productId(): ProductId
    {
        return ProductId::fromString($this->payload['product_id']);
    }

    /**
     * @throws \Domain\Product\Exception\InvalidProductName
     */
    public function name(): ProductName
    {
        return ProductName::fromString($this->payload['name']);
    }

    /**
     * @throws \Domain\Product\Exception\InvalidProductPrice
     */
    public function price(): ProductPrice
    {
        return ProductPrice::fromString($this->payload['price']);
    }

    /**
     * @throws \Domain\Product\Exception\InvalidProductStack
     */
    public function stack(): ProductStack
    {
        return ProductStack::fromString($this->payload['stack']);
    }

    /**
     * @throws \Assert\AssertionFailedException
     */
    protected function setPayload(array $payload): void
    {
        Assertion::keyExists($payload, 'product_id');
        Assertion::uuid($payload['product_id']);

        Assertion::keyExists($payload, 'name');
        Assertion::string($payload['name']);

        Assertion::keyExists($payload, 'price');
        Assertion::string($payload['price']);

        Assertion::keyExists($payload, 'stack');
        Assertion::string($payload['stack']);

        $this->payload = $payload;
    }
}
