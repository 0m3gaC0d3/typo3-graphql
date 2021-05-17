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

namespace Wpu\Graphql;

use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use ReflectionClass;
use ReflectionMethod;

/**
 * This class is a slightly modified version of the original (`ecodev/graphql-doctrine`).
 *
 * @see https://github.com/Ecodev/graphql-doctrine/blob/master/src/DefaultFieldResolver.php
 */
class DefaultFieldResolver
{
    /**
     * @param mixed   $source
     * @param mixed[] $args
     * @param mixed   $context
     *
     * @return mixed|null
     */
    public function __invoke($source, array $args, $context, ResolveInfo $info)
    {
        /** @var string $fieldName */
        $fieldName = $info->fieldName;
        $property = null;

        if (is_object($source)) {
            $property = $this->resolveObject($source, $args, $fieldName);
        } elseif (is_array($source)) {
            $property = $this->resolveArray($source, $fieldName);
        }

        return $property instanceof Closure ? $property($source, $args, $context) : $property;
    }

    /**
     * @param mixed $source
     *
     * @return mixed
     */
    private function resolveObject($source, array $args, string $fieldName)
    {
        $getter = $this->getGetter($source, $fieldName);
        if ($getter) {
            $args = $this->orderArguments($getter, $args);

            return $getter->invoke($source, ...$args);
        }

        if (isset($source->{$fieldName})) {
            return $source->{$fieldName};
        }

        return null;
    }

    /**
     * @param mixed $source
     *
     * @return mixed
     */
    private function resolveArray($source, string $fieldName)
    {
        return $source[$fieldName] ?? null;
    }

    /**
     * @param mixed $source
     */
    private function getGetter($source, string $name): ?ReflectionMethod
    {
        if (!preg_match('~^(is|has)[A-Z]~', $name)) {
            $name = 'get' . ucfirst($name);
        }

        $class = new ReflectionClass($source);
        if ($class->hasMethod($name)) {
            $method = $class->getMethod($name);
            if ($method->getModifiers() & ReflectionMethod::IS_PUBLIC) {
                return $method;
            }
        }

        return null;
    }

    private function orderArguments(ReflectionMethod $method, array $args): array
    {
        $result = [];
        if (!$args) {
            return $result;
        }

        foreach ($method->getParameters() as $param) {
            if (array_key_exists($param->getName(), $args)) {
                $result[] = $args[$param->getName()];
            }
        }

        return $result;
    }
}
