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

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\Category;
use TYPO3\CMS\Extbase\Domain\Repository\CategoryRepository;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;

class SchemaProvider implements SchemaProviderInterface
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function createSchema(array $configuration): Schema
    {
        $categoryType = new ObjectType([
            'name' => 'Category',
            'fields' => [
                'uid' => Type::int(),
                'title' => Type::string(),
            ],
        ]);

        $queryType = new ObjectType([
            'name' => 'Query',
            'fields' => [
                'category' => [
                    'type' => $categoryType,
                    'args' => [
                        'uid' => Type::nonNull(Type::id()),
                    ],
                    'resolve' => function ($rootValue, $args) {
                        /** @var Category|null $category */
                        $category = $this->categoryRepository->findByUid((int) $args['uid']);
                        if (is_null($category)) {
                            return null;
                        }

                        return $category;
                    },
                ],
                'categories' => [
                    'type' => Type::listOf($categoryType),
                    'resolve' => function () {
                        /** @var Typo3QuerySettings $settings */
                        $settings = GeneralUtility::makeInstance(Typo3QuerySettings::class);
                        $settings->setRespectStoragePage(false);
                        $this->categoryRepository->setDefaultQuerySettings($settings);

                        /** @var Category[] $categories */
                        $categories = $this->categoryRepository->findAll();

//                        if (is_null($category)) {
//                            return null;
//                        }
                        return $categories;
                    },
                ],
            ],
        ]);

        return new Schema([
            'query' => $queryType,
        ]);
    }
}
