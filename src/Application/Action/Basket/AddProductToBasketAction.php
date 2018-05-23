<?php

declare(strict_types=1);

namespace Application\Action\Basket;

use Domain\Basket\Basket;
use Domain\Basket\BasketId;
use Domain\Basket\Command\AddProductToBasket;
use Domain\Basket\Command\CreateNewBasket;
use Domain\Product\ProductId;
use Prooph\ServiceBus\CommandBus;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AddProductToBasketAction
{
    /**
     * @var CommandBus
     */
    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    /**
     * @Route(name="basket-add_product", path="/basket/{productId}", methods={"PUT"})
     *
     * @throws \Prooph\ServiceBus\Exception\CommandDispatchException
     */
    public function __invoke(string $productId, Request $request): JsonResponse
    {
        if (!$basketId = $request->getSession()->get('basketId')) {
            $basketId = BasketId::generate();

            $this->commandBus->dispatch(CreateNewBasket::withData(
                $basketId->toString()
            ));

            $request->getSession()->get('basketId', $basketId);
        }

        $this->commandBus->dispatch(AddProductToBasket::withData(
            $basketId->toString(),
            $productId
        ));

        return JsonResponse::create([
                'message' => 'ok',
            ],
            Response::HTTP_CREATED
        );
    }
}
