<?php

defined('TYPO3_MODE') || exit();

(function (string $extensionKey) {

    \Wpu\Graphql\Utility\GraphqlApiManager::register(
        'api-2',
        '/graphql',
        \Wpu\Graphql\Provider\SchemaProvider::class
    );

})('wpu_graphql');
