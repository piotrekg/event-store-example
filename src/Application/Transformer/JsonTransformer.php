<?php

declare(strict_types=1);

namespace Application\Transformer;

use Prooph\EventStore\Http\Middleware\Transformer;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\JsonResponse;

final class JsonTransformer implements Transformer
{
    /**
     * @throws \InvalidArgumentException
     */
    public function createResponse(array $result): ResponseInterface
    {
        return new JsonResponse($result);
    }
}
