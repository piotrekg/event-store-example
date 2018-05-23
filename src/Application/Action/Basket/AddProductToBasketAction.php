<?php

declare(strict_types=1);

namespace Application\Action\Basket;

use Domain\Basket\BasketId;
use Domain\Basket\Command\AddProductToBasket;
use Domain\Basket\Command\CreateNewBasket;
use Domain\Basket\Exception\BasketNotFoundException;
use Prooph\ServiceBus\CommandBus;
use Prooph\ServiceBus\Exception\CommandDispatchException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
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
     * @Route(name="basket_add_product", path="/basket/{productId}", methods={"PUT"})
     *
     * @throws \Prooph\ServiceBus\Exception\CommandDispatchException
     */
    public function __invoke(string $productId, Session $session): JsonResponse
    {
        if (!$basketId = $session->get('basketId')) {
            $basketId = BasketId::generate();

            $this->commandBus->dispatch(CreateNewBasket::withData(
                $basketId->toString()
            ));

            $session->set('basketId', $basketId);
        }

        try {
            $this->commandBus->dispatch(AddProductToBasket::withData(
                $basketId->toString(),
                $productId
            ));
        } catch (CommandDispatchException $dispatchException) {
            if ($dispatchException->getPrevious() instanceof BasketNotFoundException) {
                $this->commandBus->dispatch(CreateNewBasket::withData(
                    $basketId->toString()
                ));

                $this->commandBus->dispatch(AddProductToBasket::withData(
                    $basketId->toString(),
                    $productId
                ));
            } else {
                throw $dispatchException;
            }
        }

        return JsonResponse::create([
                'message' => 'ok',
            ],
            Response::HTTP_CREATED
        );
    }
}
