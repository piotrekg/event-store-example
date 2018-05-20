<?php

declare(strict_types=1);

namespace Application\Action\Product;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CreateNewProductActionTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\Exception
     */
    public function testSuccessProductCreation(): void
    {
        // given
        $action = new CreateNewProductAction();
        $request = $this->createMock(Request::class);

        // when
        $result = $action($request);

        // then
        $this->assertEquals(Response::HTTP_CREATED, $result->getStatusCode());
        $this->assertEquals(json_encode('ok'), $result->getContent());
    }
}
