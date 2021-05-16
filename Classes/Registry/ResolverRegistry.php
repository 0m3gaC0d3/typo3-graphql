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

namespace Wpu\Graphql\Registry;

use InvalidArgumentException;
use Wpu\Graphql\Resolver\ResolverInterface;

class ResolverRegistry extends AbstractRegistry
{
    public const TYPE = ResolverInterface::class;

    public function __construct()
    {
        parent::__construct(self::TYPE);
    }

    /**
     * @param ResolverInterface $item
     */
    public function add($item, string $key): void
    {
        if (empty($item->getType())) {
            throw new InvalidArgumentException('Method getType of given resolver can not be empty!');
        }
        if (!is_callable($item)) {
            throw new InvalidArgumentException('The given resolver ' . $item->getType() . ' is not callable! Add method __invoke to the resolver');
        }
        parent::add($item, $key);
    }
}
