<?php

defined('TYPO3_MODE') || exit();

(function (string $extensionKey) {

    \Wpu\Graphql\Utility\GraphqlApiManager::register(
        $extensionKey,
        'api-2',
        '/graphql',
        '/graphql/login',
        \Wpu\Graphql\Provider\FileBasedSchemaProvider::class,
        \Wpu\Graphql\Action\GraphqlAction::class,
        \Wpu\Graphql\Action\LoginAction::class,
        [
            'schemaFilePath' => \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName(
                "EXT:$extensionKey/Resources/Private/Graphql/schema.graphql"
            )
        ]
    );

})('wpu_graphql');
