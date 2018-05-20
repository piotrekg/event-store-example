<?php

declare(strict_types=1);

namespace Domain\Product;

use Assert\Assertion;
use Assert\AssertionFailedException;
use Domain\Product\Exception\InvalidProductName;
use Domain\ValueObject;

final class ProductName implements ValueObject
{
    /**
     * @var string
     */
    private $name;

    /**
     * @throws InvalidProductName
     */
    public static function fromString(string $name): self
    {
        return new self($name);
    }

    /**
     * @throws InvalidProductName
     */
    private function __construct(string $name)
    {
        try {
            Assertion::notEmpty($name);
        } catch (AssertionFailedException $e) {
            throw InvalidProductName::reason($e->getMessage());
        }
        $this->name = $name;
    }

    public function toString(): string
    {
        return $this->name;
    }

    public function equals(ValueObject $object): bool
    {
        return get_class($this) === get_class($object)
            && $this->name === $object->name;
    }
}
