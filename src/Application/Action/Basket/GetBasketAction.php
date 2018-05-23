<?php

declare(strict_types=1);

namespace Application\Action\Basket;

use Domain\Basket\BasketId;
use Domain\Basket\BasketRepository;
use Domain\Basket\Command\CreateNewBasket;
use Infrastructure\Basket\Projection\BasketFinder;
use Prooph\ServiceBus\CommandBus;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

final class GetBasketAction
{
    /**
     * @var CommandBus
     */
    private $commandBus;

    /**
     * @var BasketRepository
     */
    private $finder;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(
        CommandBus $commandBus,
        BasketFinder $finder,
        SerializerInterface $serializer
    ) {
        $this->commandBus = $commandBus;
        $this->finder = $finder;
        $this->serializer = $serializer;
    }

    /**
     * @Route(name="basket_get", path="/basket/", methods={"GET"})
     *
     * @throws \Prooph\ServiceBus\Exception\CommandDispatchException
     */
    public function __invoke(Session $session): JsonResponse
    {
        if (!$basketId = $session->get('basketId')) {
            $basketId = BasketId::generate();

            $this->commandBus->dispatch(CreateNewBasket::withData(
                $basketId->toString()
            ));

            $session->set('basketId', $basketId);
        }

        $basket = $this->finder->findById($basketId);

        return JsonResponse::fromJsonString(
            $this->serializer->serialize($basket, 'json'),
            Response::HTTP_CREATED
        );
    }
}
