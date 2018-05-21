<?php

declare(strict_types=1);

namespace Application\Listener;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class JsonRequestTransformerListenerTest extends TestCase
{
    /**
     * @var JsonRequestTransformerListener
     */
    private $listener;

    public function setUp()
    {
        $this->listener = new JsonRequestTransformerListener();
    }

    /**
     * @dataProvider jsonContentTypes
     *
     * @throws \LogicException
     * @throws \PHPUnit_Framework_MockObject_RuntimeException
     */
    public function testItTransformsRequestsWithAJsonContentType($contentType): void
    {
        //given
        $data = ['foo' => 'bar'];
        $request = $this->createRequest($contentType, json_encode($data));
        $event = $this->createGetResponseEventMock($request);

        // when
        $this->listener->onKernelRequest($event);

        // then
        $this->assertEquals(
            $data,
            $event->getRequest()->request->all()
        );

        $this->assertNull($event->getResponse());
    }

    /**
     * @codeCoverageIgnore
     */
    public function jsonContentTypes(): array
    {
        return [
            ['application/json'],
            ['application/x-json'],
        ];
    }

    /**
     * @throws \LogicException
     * @throws \PHPUnit_Framework_MockObject_RuntimeException
     */
    public function testItReturnsABadRequestResponseIfJsonIsInvalid(): void
    {
        // given
        $request = $this->createRequest('application/json', '{meh}');
        $event = $this->createGetResponseEventMock($request);

        // when
        $this->listener->onKernelRequest($event);

        // then
        $this->assertEquals(400, $event->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider notJsonContentTypes
     *
     * @throws \LogicException
     * @throws \PHPUnit_Framework_MockObject_RuntimeException
     */
    public function testItDoesNotTransformOtherContentTypes($contentType): void
    {
        // given
        $request = $this->createRequest($contentType, 'some=body');
        $event = $this->createGetResponseEventMock($request);

        // when
        $this->listener->onKernelRequest($event);

        // then
        $this->assertEquals($request, $event->getRequest());
        $this->assertNull($event->getResponse());
    }

    /**
     * @throws \LogicException
     * @throws \PHPUnit_Framework_MockObject_RuntimeException
     */
    public function testItDoesNotReplaceRequestDataIfThereIsNone(): void
    {
        // given
        $request = $this->createRequest('application/json', '');
        $event = $this->createGetResponseEventMock($request);

        // when
        $this->listener->onKernelRequest($event);

        // then
        $this->assertEquals($request, $event->getRequest());
        $this->assertNull($event->getResponse());
    }

    /**
     * @throws \LogicException
     * @throws \PHPUnit_Framework_MockObject_RuntimeException
     */
    public function testItDoesNotReplaceRequestDataIfContentIsJsonNull(): void
    {
        // given
        $request = $this->createRequest('application/json', 'null');
        $event = $this->createGetResponseEventMock($request);

        // when
        $this->listener->onKernelRequest($event);

        // then
        $this->assertEquals($request, $event->getRequest());
        $this->assertNull($event->getResponse());
    }

    /**
     * @codeCoverageIgnore
     */
    public function notJsonContentTypes(): array
    {
        return [
            ['application/x-www-form-urlencoded'],
            ['text/html'],
            ['text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8'],
        ];
    }

    private function createRequest($contentType, $body): Request
    {
        $request = new Request([], [], [], [], [], [], $body);
        $request->headers->set('CONTENT_TYPE', $contentType);

        return $request;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject
     *
     * @throws \PHPUnit_Framework_MockObject_RuntimeException
     */
    private function createGetResponseEventMock(Request $request): GetResponseEvent
    {
        $event = $this->getMockBuilder(GetResponseEvent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRequest'])
            ->getMock()
        ;

        $event->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request))
        ;

        return $event;
    }
}
