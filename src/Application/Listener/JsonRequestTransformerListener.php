<?php

declare(strict_types=1);

namespace Application\Listener;

use Doctrine\Common\Util\Inflector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class JsonRequestTransformerListener
{
    /**
     * @throws \LogicException
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $content = $request->getContent();

        if (empty($content)) {
            return;
        }

        if (!$this->isJsonRequest($request)) {
            return;
        }

        if (!$this->transformJsonBody($request)) {
            $response = Response::create(
                'Unable to parse request.',
                Response::HTTP_BAD_REQUEST
            );
            $event->setResponse($response);
        }
    }

    private function isJsonRequest(Request $request): bool
    {
        return 'json' === $request->getContentType();
    }

    /**
     * @throws \LogicException
     */
    private function transformJsonBody(Request $request): bool
    {
        $data = json_decode($request->getContent(), true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            return false;
        }

        if (null === $data) {
            return true;
        }

        $request->request->replace($this->camelize($data));

        return true;
    }

    private function camelize(array $data): array
    {
        return array_combine(array_map(function ($k) {
            return Inflector::camelize($k);
        }, array_keys($data)), $data);
    }
}
