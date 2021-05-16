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

namespace Wpu\Graphql;

use Wpu\Graphql\Event\CollectionResolversEvent;
use Wpu\Graphql\Resolver\QueryResolver;

class TestEventListener
{
    /**
     * @var QueryResolver
     */
    private $queryResolver;

    public function __construct(QueryResolver $queryResolver)
    {
        $this->queryResolver = $queryResolver;
    }

    public function __invoke(CollectionResolversEvent $event): void
    {
        $event->getResolverRegistry()->add($this->queryResolver, $this->queryResolver->getType());
    }
}
