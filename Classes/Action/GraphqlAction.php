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

namespace Wpu\Graphql\Action;

use Exception;
use GraphQL\Error\FormattedError;
use GraphQL\Server\ServerConfig;
use GraphQL\Server\StandardServer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Wpu\Graphql\Handler\RequestAuthHandlerInterface;
use Wpu\Graphql\Provider\DebugFlagProviderInterface;
use Wpu\Graphql\Provider\SchemaProviderInterface;

class GraphqlAction implements ActionInterface
{
    /**
     * @var RequestAuthHandlerInterface
     */
    private $requestAuthHandler;

    /**
     * @var DebugFlagProviderInterface
     */
    private $debugFlagProvider;

    public function __construct(RequestAuthHandlerInterface $requestAuthHandler, DebugFlagProviderInterface $debugFlagProvider)
    {
        $this->requestAuthHandler = $requestAuthHandler;
        $this->debugFlagProvider = $debugFlagProvider;
    }

    public function process(ServerRequestInterface $request, ResponseInterface $response, array $configuration = []): ResponseInterface
    {
        $debug = $this->debugFlagProvider->getDebugFlag();
//        $graphQLSyncPromiseAdapter = new SyncPromiseAdapter();
//        $promiseAdapter = new WebonyxGraphQLSyncPromiseAdapter($graphQLSyncPromiseAdapter);
//        $this->dispatchDataLoaderEvent($promiseAdapter);
        try {
            $request = $this->requestAuthHandler->handleRequest($request);
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
            $response->getBody()->write((string) json_encode([
                'errors' => FormattedError::createFromException($exception, $debug),
            ]));

            return $response->withStatus(200)->withHeader('Content-type', 'application/json');
        }

//
//        $response->getBody()->write('lol');
//        return $response;
    }
}
