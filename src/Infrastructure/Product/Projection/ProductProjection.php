<?php

declare(strict_types=1);

namespace Infrastructure\Product\Projection;

use Domain\Product\Event\ProductWasCreated;
use Prooph\Bundle\EventStore\Projection\ReadModelProjection;
use Prooph\EventStore\Projection\ReadModelProjector;

final class ProductProjection implements ReadModelProjection
{
    public function project(ReadModelProjector $projector): ReadModelProjector
    {
        $projector->fromStream('event_stream')
            ->when([
                ProductWasCreated::class => function ($state, ProductWasCreated $event) {
                    /** @var ProductReadModel $readModel */
                    $readModel = $this->readModel();
                    $readModel->stack('insert', [
                        'id' => $event->productId()->toString(),
                        'name' => $event->name()->toString(),
                        'price' => $event->price()->toString(),
                        'stock' => $event->stock()->toString(),
                    ]);
                },
            ]);

        return $projector;
    }
}
