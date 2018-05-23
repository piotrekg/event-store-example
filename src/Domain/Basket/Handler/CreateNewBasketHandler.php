<?php

declare(strict_types=1);

namespace Domain\Basket\Handler;

use Domain\Basket\Basket;
use Domain\Basket\BasketRepository;
use Domain\Basket\Command\CreateNewBasket;

class CreateNewBasketHandler
{
    /**
     * @var BasketRepository
     */
    private $repository;

    public function __construct(BasketRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(CreateNewBasket $command): void
    {
        // if (null !== $this->repository->get($command->basketId())) {
        //     throw BasketAlreadyExists::withBasketId($command->basketId());
        // }

        $basket = Basket::createBasket(
            $command->basketId()
        );

        $this->repository->save($basket);
    }
}
