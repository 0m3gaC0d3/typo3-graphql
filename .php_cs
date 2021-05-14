<?php
$projectName = basename(__DIR__);
$year = date('Y');
$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__ . '/Classes/',
        __DIR__ . '/Tests/',
    ])
    ->exclude(__DIR__ . '/.Build/');

$header = <<<EOF
This file is part of the "$projectName" Extension for TYPO3 CMS.

For the full copyright and license information, please read the
LICENSE.txt file that was distributed with this source code.

(c) $year Wolf Utz <wpu@hotmail.de>
EOF;

return PhpCsFixer\Config::create()
    ->setUsingCache(false)
    ->setRules([
        '@PSR1' => true,
        '@PSR2' => true,
        '@Symfony' => true,
        'header_comment' => [
            'header' => $header,
            'location' => 'after_open',
            'separate' => 'both',
            'commentType' => 'PHPDoc',
        ],
        'no_useless_else' => true,
        'no_useless_return' => true,
        'no_unused_imports' => true,
        'ordered_class_elements' => true,
        'ordered_imports' => true,
        'phpdoc_order' => true,
        'phpdoc_summary' => false,
        'blank_line_after_opening_tag' => false,
        'concat_space' => ['spacing' => 'one'],
        'array_syntax' => ['syntax' => 'short'],
        'yoda_style' => true,
        'declare_strict_types' => true,
        'psr4' => true,
        'no_php4_constructor' => true,
        'no_short_echo_tag' => true,
        'semicolon_after_instruction' => true,
        'align_multiline_comment' => true,
        'general_phpdoc_annotation_remove' => ['annotations' => ["author", "package"]],
        'phpdoc_add_missing_param_annotation' => ['only_untyped' => false],
    ])
    ->setFinder($finder);
