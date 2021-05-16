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
use GraphQL\GraphQL;
use GraphQL\Server\ServerConfig;
use GraphQL\Server\StandardServer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Extbase\Object\Container\Container;
use Wpu\Graphql\DefaultFieldResolver;
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

    /**
     * @var Container
     */
    private $container;

    public function __construct(
        RequestAuthHandlerInterface $requestAuthHandler,
        DebugFlagProviderInterface $debugFlagProvider,
        Container $container
    ) {
        $this->requestAuthHandler = $requestAuthHandler;
        $this->debugFlagProvider = $debugFlagProvider;
        $this->container = $container;
    }

    public function process(ServerRequestInterface $request, ResponseInterface $response, array $configuration = []): ResponseInterface
    {
        $debug = $this->debugFlagProvider->getDebugFlag();
//        $graphQLSyncPromiseAdapter = new SyncPromiseAdapter();
//        $promiseAdapter = new WebonyxGraphQLSyncPromiseAdapter($graphQLSyncPromiseAdapter);
        try {
            $request = $this->requestAuthHandler->handleRequest($request);
            GraphQL::setDefaultFieldResolver(new DefaultFieldResolver());
            /** @var SchemaProviderInterface $schemaProvider */
            $schemaProvider = $this->container->getInstance($configuration['schemaProvider']);
            $schema = $schemaProvider->createSchema($configuration);
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

            return $response->withStatus($response->getStatusCode())->withHeader('Content-Type', 'application/json');
        } catch (Exception $exception) {
            $response->getBody()->write((string) json_encode([
                'errors' => FormattedError::createFromException($exception, $debug),
            ]));

            return $response->withStatus(200)->withHeader('Content-type', 'application/json');
        }
    }
}
