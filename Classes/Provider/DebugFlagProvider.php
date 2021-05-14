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

namespace Wpu\Graphql\Provider;

use GraphQL\Error\DebugFlag;
use TYPO3\CMS\Core\Core\ApplicationContext;

class DebugFlagProvider implements DebugFlagProviderInterface
{
    /**
     * @var ApplicationContext
     */
    private $applicationContext;

    public function __construct(ApplicationContext $applicationContext)
    {
        $this->applicationContext = $applicationContext;
    }

    public function getDebugFlag(): int
    {
        if ($this->applicationContext->isDevelopment()) {
            return DebugFlag::INCLUDE_DEBUG_MESSAGE | DebugFlag::INCLUDE_TRACE;
        }

        return DebugFlag::NONE;
    }
}
