<?php

declare(strict_types=1);

namespace Application\Helper;

use Prooph\EventStore\Http\Middleware\UrlHelper;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class SymfonyUrlHelper implements UrlHelper
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     */
    public function generate(string $urlId, array $params = []): string
    {
        return $this->urlGenerator->generate($urlId, $params);
    }
}
