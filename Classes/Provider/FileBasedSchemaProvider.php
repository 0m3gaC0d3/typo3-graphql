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

namespace Wpu\Graphql\Provider;

use GraphQL\Language\AST\DocumentNode;
use GraphQL\Language\AST\Node;
use GraphQL\Language\Parser;
use GraphQL\Type\Schema;
use GraphQL\Utils\AST;
use GraphQL\Utils\BuildSchema;
use InvalidArgumentException;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Core\ApplicationContext;
use TYPO3\CMS\Core\Core\Environment;
use Wpu\Graphql\Event\CollectResolversEvent;
use Wpu\Graphql\Registry\ResolverRegistry;

class FileBasedSchemaProvider implements SchemaProviderInterface
{
    private const CACHE_FILE_NAME = 'graphql_schema.php';

    /**
     * @var ResolverRegistry
     */
    private $resolverRegistry;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var ApplicationContext
     */
    private $applicationContext;

    public function __construct(
        ResolverRegistry $resolverRegistry,
        EventDispatcherInterface $eventDispatcher,
        ApplicationContext $applicationContext
    ) {
        $this->resolverRegistry = $resolverRegistry;
        $this->eventDispatcher = $eventDispatcher;
        $this->applicationContext = $applicationContext;
    }

    public function createSchema(array $configuration): Schema
    {
        $schemaFilePath = $configuration['options']['schemaFilePath'] ?? null;
        if (is_null($schemaFilePath)) {
            throw new InvalidArgumentException('Missing required option schemaFilePath!');
        }
        if (!file_exists($configuration['options']['schemaFilePath'])) {
            throw new InvalidArgumentException("Schema file \"$schemaFilePath\" does not exist!");
        }

        // Collect resolvers.
        $this->eventDispatcher->dispatch(new CollectResolversEvent($this->resolverRegistry));

        /** @var DocumentNode $source */
        $source = $this->getSchemaSource($configuration['options']['schemaFilePath']);

        return BuildSchema::build($source, $this->buildTypeConfiguration(), ['commentDescriptions' => true]);
    }

    private function getSchemaSource(string $schemaFilePath): Node
    {
        $cacheFilePath = Environment::getVarPath() . '/cache/' . self::CACHE_FILE_NAME;

        // Check for cached version of schema in production environments.
        if ($this->applicationContext->isProduction() && file_exists($cacheFilePath)) {
            return AST::fromArray(require $cacheFilePath);
        }

        $document = Parser::parse((string) file_get_contents($schemaFilePath));

        // Cache schema in production environments.
        if ($this->applicationContext->isProduction()) {
            file_put_contents(
                $cacheFilePath,
                "<?php\nreturn " . var_export(AST::toArray($document), true) . ";\n"
            );
        }

        return $document;
    }

    private function buildTypeConfiguration(): callable
    {
        $resolverRegistry = $this->resolverRegistry;
        $typeConfigDecorator = function (&$typeConfig) use ($resolverRegistry) {
            $type = $typeConfig['name'];
            if (!$resolverRegistry->has($type)) {
                return $typeConfig;
            }
            $resolver = $resolverRegistry->get($type);
            $typeConfig['resolveField'] = $resolver;

            return $typeConfig;
        };

        return $typeConfigDecorator;
    }
}
