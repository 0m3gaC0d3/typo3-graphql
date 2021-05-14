<?php

defined('TYPO3_MODE') || exit();

(function (string $extensionKey) {

    \Wpu\Graphql\Utility\GraphqlApiManager::register(
        'api-2',
        '/graphql',
        '/graphql/login',
        \Wpu\Graphql\Provider\SchemaProvider::class,
        \Wpu\Graphql\Action\GraphqlAction::class,
        \Wpu\Graphql\Action\LoginAction::class
    );

})('wpu_graphql');
