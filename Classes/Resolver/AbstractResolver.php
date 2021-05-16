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

namespace Wpu\Graphql\Resolver;

use GraphQL\Type\Definition\ResolveInfo;

abstract class AbstractResolver implements ResolverInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke($parent, array $args, $context, ResolveInfo $info)
    {
        $resolverMethod = 'resolve' . ucfirst($info->fieldName);
        if (method_exists($this, $resolverMethod)) {
            return $this->$resolverMethod($parent, $args, $context, $info);
        }

        return $parent[$info->fieldName] ?? null;
    }
}
