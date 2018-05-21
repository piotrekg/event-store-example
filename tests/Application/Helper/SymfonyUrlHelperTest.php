<?php

declare(strict_types=1);

namespace Application\Helper;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SymfonyUrlHelperTest extends TestCase
{
    /**
     * @throws \LogicException
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     */
    public function testGenerate(): void
    {
        // given
        $url = 'some_url';
        $params = ['a' => 1, 'b' => 2];
        $resultUrl = 'some_url?a=1&b=2';

        $generator = $this->prophesize(UrlGeneratorInterface::class);

        $generator
            ->generate($url, $params)
            ->shouldBeCalledTimes(1)
            ->willReturn($resultUrl)
        ;

        $helper = new SymfonyUrlHelper($generator->reveal());

        // when
        $result = $helper->generate($url, $params);

        // then
        $this->assertEquals($resultUrl, $result);
    }
}
