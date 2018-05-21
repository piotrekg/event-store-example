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
     * @var int
     */
    private $stock;

    /**
     * @throws InvalidProductStock
     */
    public static function fromString(string $stock): self
    {
        return new self((int) $stock);
    }

    /**
     * @throws InvalidProductStock
     */
    private function __construct(int $stock)
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

    public function inStock(): bool
    {
        return $this->stock > 0;
    }

    public function decrease(): self
    {
        $object = clone $this;
        --$object->stock;

        return $object;
    }

    public function increase(): self
    {
        $object = clone $this;
        ++$object->stock;

        return $object;
    }

    public function get(): int
    {
        return $this->stock;
    }
}
