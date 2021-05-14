<?php

/**
 * This file is part of the "typo3-graphql" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2021 Wolf Utz <wpu@hotmail.de>
 */

declare(strict_types=1);

namespace Wpu\Graphql\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\ResponseFactory;
use TYPO3\CMS\Extbase\Object\Container\Container;
use Wpu\Graphql\Action\ActionInterface;

class GraphqlMiddleware implements MiddlewareInterface
{
    /**
     * @var ResponseFactory
     */
    private $responseFactory;

    /**
     * @var Container
     */
    private $container;

    public function __construct(ResponseFactory $responseFactory, Container $container)
    {
        $this->responseFactory = $responseFactory;
        $this->container = $container;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Get configured apis.
        $apis = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['extbase']['extensions']['WpuGraphql']['apis'];
        if (0 === count($apis)) {
            // No configured apis found, so return next middleware.
            return $handler->handle($request);
        }

        // Check current request against configured apis.
        $targetApiIdentifier = null;
        foreach ($apis as $identifier => $api) {
            // Check, if the current requests is a configured login endpoint.
            if ($api['loginEndpoint'] === $request->getUri()->getPath()) {
                $action = $this->getAction($api['loginAction']);

                $response = $this->responseFactory->createResponse();
                $request = $request->withParsedBody(json_decode($request->getBody()->getContents(), true));

                return $action->process($request, $response, $api);
            }

            // Check, if the current requests is a configured graphql endpoint.
            if ($api['graphqlEndpoint'] === $request->getUri()->getPath()) {
                $action = $this->getAction($api['graphqlAction']);

                $response = $this->responseFactory->createResponse();
                $request = $request->withParsedBody(json_decode($request->getBody()->getContents(), true));

                return $action->process($request, $response, $api);
            }
        }

        // Could not find a configured api matching the current request.
        return $handler->handle($request);
    }

    private function getAction(string $actionClass): ActionInterface
    {
        /** @var ActionInterface $action */
        $action = $this->container->getInstance($actionClass);

        if (!$action instanceof ActionInterface) {
            throw new \InvalidArgumentException("Class \"$actionClass\" must implement \"" . ActionInterface::class . '".');
        }

        return $action;
    }
}
