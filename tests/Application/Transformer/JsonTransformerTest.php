<?php

declare(strict_types=1);

namespace Application\Transformer;

use PHPUnit\Framework\TestCase;
use Zend\Diactoros\Response\JsonResponse;

class JsonTransformerTest extends TestCase
{
    /**
     * @throws \InvalidArgumentException
     */
    public function testCreateResponse(): void
    {
        // given
        $data = [1, 2, 3];
        $transformer = new JsonTransformer();

        // when
        $result = $transformer->createResponse($data);

        // then
        $this->assertEquals((new JsonResponse($data))->getPayload(), $result->getPayload());
    }
}
