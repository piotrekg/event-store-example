<?php

declare(strict_types=1);

namespace Application\Action\Product;

use Domain\Product\Command\CreateNewProduct;
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
            (string) $request->get('name'),
            (float) $request->get('price')
        ));

        return JsonResponse::create([
                'id' => $id->toString(),
                'message' => 'ok',
            ],
            Response::HTTP_CREATED
        );
    }
}
