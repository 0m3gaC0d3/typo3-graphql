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

namespace Wpu\Graphql\Resolver;

use GraphQL\Type\Definition\ResolveInfo;

interface ResolverInterface
{
    /**
     * @param mixed $parent
     * @param mixed $context
     *
     * @return mixed
     */
    public function __invoke($parent, array $args, $context, ResolveInfo $info);

    public function getType(): string;
}
