<?php

return [
    'frontend' => [
        'graphql-api' => [
            'target' => \Wpu\Graphql\Middleware\GraphqlMiddleware::class,
            'before' => [
                'typo3/cms-frontend/timetracker',
            ],
        ],
    ]
];
