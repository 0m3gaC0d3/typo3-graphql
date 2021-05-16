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

namespace Wpu\Graphql\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Repository\CategoryRepository;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;

class QueryResolver extends AbstractResolver
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->setupCategoryRepository();
    }

    public function getType(): string
    {
        return 'Query';
    }

    protected function resolveCategory($root, array $args, $context, ResolveInfo $info)
    {
        $uid = (int) $args['uid'];

        return $this->categoryRepository->findByUid($uid);
    }

    protected function resolveCategories($root, array $args, $context, ResolveInfo $info)
    {
        return $this->categoryRepository->findAll();
    }

    private function setupCategoryRepository(): void
    {
        /** @var Typo3QuerySettings $settings */
        $settings = GeneralUtility::makeInstance(Typo3QuerySettings::class);
        $settings->setRespectStoragePage(false);
        $this->categoryRepository->setDefaultQuerySettings($settings);
    }
}
