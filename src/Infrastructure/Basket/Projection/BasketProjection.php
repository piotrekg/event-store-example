<?php

declare(strict_types=1);

namespace Infrastructure\Basket\Projection;

use Domain\Basket\Event\BasketWasCreated;
use Domain\Basket\Event\ProductAddedToBasket;
use Prooph\Bundle\EventStore\Projection\ReadModelProjection;
use Prooph\EventStore\Projection\ReadModelProjector;

final class BasketProjection implements ReadModelProjection
{
    public function project(ReadModelProjector $projector): ReadModelProjector
    {
        $projector->fromStream('event_stream')
            ->when([
                BasketWasCreated::class => function ($state, BasketWasCreated $event) {
                    /** @var BasketReadModel $readModel */
                    $readModel = $this->readModel();
                    $readModel->stack('insert', [
                        'id' => $event->basketId()->toString(),
                    ]);
                },
                ProductAddedToBasket::class => function ($state, ProductAddedToBasket $event) {
                    /** @var BasketReadModel $readModel */
                    $readModel = $this->readModel();
                    $readModel->stack('addProduct',
                        $event->basketId()->toString(),
                        $event->productId()->toString()
                    );
                },
            ])
        ;

        return $projector;
    }
}
