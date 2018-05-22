<?php

declare(strict_types=1);

namespace Application\Action\Product;

use Application\Exception\ProductNotFoundException;
use Domain\Product\BasketId;
use Domain\Product\ProductId;
use Infrastructure\Product\Projection\ProductFinder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class GetProductAction
{
    /**
     * @var ProductFinder
     */
    private $finder;

    public function __construct(ProductFinder $finder)
    {
        $this->finder = $finder;
    }

    /**
     * @Route(name="product_get", path="/products/{productId}", methods={"GET"})
     *
     * @throws ProductNotFoundException
     */
    public function __invoke(string $productId): JsonResponse
    {
        $productId = ProductId::fromString($productId);
        $product = $this->finder->findById($productId);

        if (null === $product) {
            throw new ProductNotFoundException($productId);
        }

        return JsonResponse::create($product, Response::HTTP_OK);
    }
}
