<?php

declare(strict_types=1);

namespace Application\Action\Product;

use Domain\Product\Command\CreateNewProduct;
use Domain\Product\BasketId;
use Domain\Product\ProductId;
use Prooph\ServiceBus\CommandBus;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class CreateNewProductAction
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
     * @Route(name="product_create", path="/products/", methods={"POST"})
     *
     * @throws \Prooph\ServiceBus\Exception\CommandDispatchException
     */
    public function __invoke(Request $request): JsonResponse
    {
        $id = ProductId::generate();
        $this->commandBus->dispatch(CreateNewProduct::withData(
            $id->toString(),
            $request->get('name'),
            $request->get('price'),
            $request->get('stock')
        ));

        return JsonResponse::create([
                'id' => $id->toString(),
                'message' => 'ok',
            ],
            Response::HTTP_CREATED
        );
    }
}
