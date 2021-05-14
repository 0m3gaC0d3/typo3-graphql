<?php

/**
 * This file is part of the "wpu_graphql" Extension for TYPO3 CMS.
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
use TYPO3\CMS\Core\SysLog\Action\Login;
use Wpu\Graphql\Action\ActionInterface;
use Wpu\Graphql\Action\GraphqlAction;
use Wpu\Graphql\Action\LoginAction;

class GraphqlMiddleware implements MiddlewareInterface
{
    /**
     * @var ResponseFactory
     */
    private $responseFactory;

    /**
     * @var GraphqlAction
     */
    private $action;

    /**
     * @var LoginAction
     */
    private $loginAction;

    public function __construct(ResponseFactory $responseFactory, GraphqlAction $action, LoginAction $loginAction)
    {
        $this->responseFactory = $responseFactory;
        $this->action = $action;
        $this->loginAction = $loginAction;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Get configured apis.
        $apis = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['extbase']['extensions']['WpuGraphql']['apis'];
        if (0 === count($apis)) {
            // No configured apis found, so return next middleware.
            return $handler->handle($request);
        }


        // @todo move and fix me
        if ($request->getUri()->getPath() === '/graphql/auth') {
            $response = $this->responseFactory->createResponse();
            $request = $request->withParsedBody(json_decode($request->getBody()->getContents(), true));

            return $this->loginAction->process($request, $response);
        }


        // Check current request against configured apis.
        $targetApiIdentifier = null;
        foreach ($apis as $identifier => $api) {
            if ($api['endpoint'] !== $request->getUri()->getPath()) {
                continue;
            }
            $targetApiIdentifier = $identifier;
            break;
        }

        if (is_null($targetApiIdentifier)) {
            // No configured api endpoint matches requested url path.
            return $handler->handle($request);
        }

        $response = $this->responseFactory->createResponse();
        $request = $request->withParsedBody(json_decode($request->getBody()->getContents(), true));

        return $this->action->process($request, $response, $apis[$targetApiIdentifier]);
    }
}
