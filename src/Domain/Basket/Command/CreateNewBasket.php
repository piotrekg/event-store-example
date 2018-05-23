<?php

declare(strict_types=1);

namespace Domain\Basket\Command;

use Assert\Assertion;
use Domain\Basket\BasketId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class CreateNewBasket extends Command implements PayloadConstructable
{
    use PayloadTrait;

    public static function withData(string $basketId): self
    {
        return new self([
            'basket_id' => $basketId,
        ]);
    }

    public function basketId(): BasketId
    {
        return BasketId::fromString($this->payload['basket_id']);
    }

    /**
     * @throws \Assert\AssertionFailedException
     */
    protected function setPayload(array $payload): void
    {
        Assertion::keyExists($payload, 'basket_id');
        Assertion::uuid($payload['basket_id']);

        $this->payload = $payload;
    }
}
