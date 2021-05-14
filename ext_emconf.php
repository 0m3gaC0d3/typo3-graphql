<?php
/**
 * This file is part of the "wpu_graphql" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2021 Wolf Utz <wpu@hotmail.de>
 */

$EM_CONF['wpu_graphql'] = [
    'title' => 'Graphql integration',
    'description' => 'This extension enables you to quickly implement a graphql api TYPO3 extension.',
    'version' => '1.0.0',
    'category' => 'plugin',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.0-10.4.99',
        ]
    ],
    'state' => 'stable',
    'uploadfolder' => false,
    'clearCacheOnLoad' => true,
    'author' => 'Wolf Utz',
    'author_email' => 'wpu@hotmail.de',
    'author_company' => ''
];
