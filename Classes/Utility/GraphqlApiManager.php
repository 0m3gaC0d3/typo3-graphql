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

namespace Wpu\Graphql\Utility;

use Wpu\Graphql\Action\GraphqlAction;

class GraphqlApiManager
{
    public static function register(
        string $extensionKey,
        string $identifier,
        string $graphqlEndpoint,
        string $loginEndpoint,
        string $schemaProvider,
        string $graphqlAction = GraphqlAction::class,
        string $loginAction = GraphqlAction::class,
        array $options = []
    ): void {
        $api = [
            'extensionKey' => $extensionKey,
            'identifier' => $identifier,
            'graphqlEndpoint' => $graphqlEndpoint,
            'loginEndpoint' => $loginEndpoint,

            'schemaProvider' => $schemaProvider,
            'graphqlAction' => $graphqlAction,
            'loginAction' => $loginAction,
            'options' => $options,
        ];
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['extbase']['extensions']['WpuGraphql']['apis']["$extensionKey-$identifier"] = $api;
    }
}
