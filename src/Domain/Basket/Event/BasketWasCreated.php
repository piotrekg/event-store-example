<?php

declare(strict_types=1);

namespace Domain\Basket\Event;

use Domain\Basket\BasketId;
use Prooph\EventSourcing\AggregateChanged;

class BasketWasCreated extends AggregateChanged
{
    /**
     * @var BasketId
     */
    private $basketId;

    public static function create(BasketId $basketId)
    {
        /** @var self $event */
        $event = self::occur($basketId->toString());

        $event->basketId = $basketId;

        return $event;
    }

    public function basketId(): BasketId
    {
        if (null === $this->basketId) {
            $this->basketId = BasketId::fromString($this->aggregateId());
        }

        return $this->basketId;
    }
}
