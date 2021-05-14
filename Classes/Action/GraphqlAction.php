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

namespace Wpu\Graphql\Action;

use GraphQL\Error\DebugFlag;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


use ErrorException;
use Exception;
use GraphQL\Error\FormattedError;
use GraphQL\Server\ServerConfig;
use GraphQL\Server\StandardServer;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Symfony\Component\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Wpu\Graphql\Provider\AuthProviderInterface;
use Wpu\Graphql\Provider\SchemaProviderInterface;

class GraphqlAction implements ActionInterface
{
    /**
     * @var AuthProviderInterface
     */
    private $authProvider;

    public function __construct(AuthProviderInterface $authProvider)
    {
        $this->authProvider = $authProvider;
    }


    public function process(ServerRequestInterface $request, ResponseInterface $response, array $configuration = []): ResponseInterface
    {
        $request = $this->authProvider->handleRequest($request);
        set_error_handler(function ($severity, $message, $file, $line) {
            throw new ErrorException($message, 0, $severity, $file, $line);
        });



//        $debug = DebugUtility::getDebugFlagByEnv();
        $debug = DebugFlag::INCLUDE_DEBUG_MESSAGE | DebugFlag::INCLUDE_TRACE;
//        $graphQLSyncPromiseAdapter = new SyncPromiseAdapter();
//        $promiseAdapter = new WebonyxGraphQLSyncPromiseAdapter($graphQLSyncPromiseAdapter);
//        $this->dispatchDataLoaderEvent($promiseAdapter);
        try {
//            (new RequestValidator())->validate($request, (array) $request->getParsedBody());
            /** @var SchemaProviderInterface $schemaProvider */
            $schemaProvider = GeneralUtility::makeInstance($configuration['schemaProvider']);
            $schema = $schemaProvider->createSchema();
//            $context = $this->buildContext($request);
            $config = ServerConfig::create()
                ->setSchema($schema)
//                ->setContext($context)
                ->setErrorFormatter(function (Exception $error) use ($debug) {
                    return FormattedError::createFromException($error, $debug);
                })
                ->setQueryBatching(true);
                //->setPromiseAdapter($graphQLSyncPromiseAdapter);

            // Create server.
            $server = new StandardServer($config);
            /** @var Response $response */
            $response = $server->processPsrRequest($request, $response, $response->getBody());
//            $response = $this->dispatchGraphQLResponseCreatedEvent($response);

            return $response->withStatus($response->getStatusCode())->withHeader('Content-Type', 'application/json');
        } catch (Exception $exception) {
            $httpStatus = 500;
            $response->getBody()->write((string) json_encode([
                'errors' => FormattedError::createFromException($exception, $debug)
            ]));

            return $response->withStatus($httpStatus)->withHeader('Content-type', 'application/json');
        }

//
//        $response->getBody()->write('lol');
//        return $response;
    }
}
