<?php

declare(strict_types=1);

namespace Domain\Product;

use Assert\Assertion;
use Assert\AssertionFailedException;
use Domain\Product\Exception\InvalidProductPrice;
use Domain\ValueObject;

final class ProductPrice implements ValueObject
{
    /**
     * @var float
     */
    private $price;

    /**
     * @throws InvalidProductPrice
     */
    public static function fromString(string $price): self
    {
        return new self((float) $price);
    }

    /**
     * @throws InvalidProductPrice
     */
    public static function fromFloat(float $price): self
    {
        return new self($price);
    }

    /**
     * @throws InvalidProductPrice
     */
    private function __construct(float $price)
    {
        try {
            Assertion::notEmpty($price);
            Assertion::min($price, 0);
        } catch (AssertionFailedException $e) {
            throw InvalidProductPrice::reason($e->getMessage());
        }

        $this->price = $price;
    }

    public function toString(): string
    {
        return (string) $this->price;
    }

    public function equals(ValueObject $object): bool
    {
        return get_class($this) === get_class($object)
            && $this->price === $object->price;
    }
}
