<?php

declare(strict_types=1);

namespace Domain\Product;

use Assert\Assertion;
use Assert\AssertionFailedException;
use Domain\Product\Exception\InvalidProductName;
use Domain\Product\Exception\InvalidProductStack;
use Domain\ValueObject;

final class ProductStack implements ValueObject
{
    /**
     * @var int
     */
    private $stack;

    /**
     * @throws InvalidProductStack
     */
    public static function fromString(string $stack): self
    {
        return new self((int) $stack);
    }

    /**
     * @throws InvalidProductStack
     */
    private function __construct(int $stack)
    {
        try {
            Assertion::min($stack, 0);
        } catch (AssertionFailedException $e) {
            throw InvalidProductStack::reason($e->getMessage());
        }
        $this->stack = $stack;
    }

    public function toString(): string
    {
        return (string) $this->stack;
    }

    public function equals(ValueObject $object): bool
    {
        return get_class($this) === get_class($object)
            && $this->stack === $object->stack;
    }

    public function inStack(): bool
    {
        return $this->stack > 0;
    }

    public function decrease(): self
    {
        $object = clone $this;
        --$object->stack;

        return $object;
    }

    public function increase(): self
    {
        $object = clone $this;
        ++$object->stack;

        return $object;
    }

    public function get(): int
    {
        return $this->stack;
    }
}
