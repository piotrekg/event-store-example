<?php

declare(strict_types=1);

namespace Domain\Basket;

use Domain\ValueObject;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class BasketId implements ValueObject
{
    /**
     * @var UuidInterface
     */
    private $uuid;

    public static function generate(): BasketId
    {
        return new self(Uuid::uuid4());
    }

    private function __construct(UuidInterface $uuid)
    {
        $this->uuid = $uuid;
    }

    public static function fromString(string $productId): BasketId
    {
        return new self(Uuid::fromString($productId));
    }

    public function toString(): string
    {
        return $this->uuid->toString();
    }

    public function equals(ValueObject $other): bool
    {
        return get_class($this) === get_class($other)
            && $this->uuid->equals($other->uuid);
    }
}
