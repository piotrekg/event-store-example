<?php

declare(strict_types=1);

namespace Domain\Product;

use Assert\Assertion;
use Assert\AssertionFailedException;
use Domain\Product\Exception\InvalidProductName;
use Domain\Product\Exception\InvalidProductStock;
use Domain\ValueObject;

final class ProductStock implements ValueObject
{
    /**
     * @var string
     */
    private $stock;

    /**
     * @throws InvalidProductStock
     */
    public static function fromString(string $stock): self
    {
        return new self($stock);
    }

    /**
     * @throws InvalidProductStock
     */
    private function __construct(string $stock)
    {
        try {
            Assertion::min($stock, 0);
        } catch (AssertionFailedException $e) {
            throw InvalidProductStock::reason($e->getMessage());
        }
        $this->stock = $stock;
    }

    public function toString(): string
    {
        return (string) $this->stock;
    }

    public function equals(ValueObject $object): bool
    {
        return get_class($this) === get_class($object)
            && $this->stock === $object->stock;
    }
}
