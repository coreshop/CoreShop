<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\CoreBundle\Fixtures\Data\Demo;

use CoreShop\Component\Core\Model\CategoryInterface;
use CoreShop\Component\Core\Repository\CategoryRepositoryInterface;
use CoreShop\Component\Pimcore\DataObject\ObjectServiceInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Store\Repository\StoreRepositoryInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Pimcore\Model\DataObject\Service;
use Pimcore\Tool;

class CategoryFixture extends Fixture implements FixtureGroupInterface
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository,
        private StoreRepositoryInterface $storeRepository,
        private FactoryInterface $categoryFactory,
        private ObjectServiceInterface $objectService,
    ) {
    }

    public static function getGroups(): array
    {
        return ['demo'];
    }

    public function load(ObjectManager $manager): void
    {
        $names = [
            [
                'de' => 'Bücher',
                'en' => 'Books',
            ],
            [
                'de' => 'Computer',
                'en' => 'Computer',
            ],
            [
                'de' => 'Filme',
                'en' => 'Movies',
            ],
            [
                'de' => 'Kleidung',
                'en' => 'Clothing',
            ],
            [
                'de' => 'Schuhe',
                'en' => 'Shoes',
            ],
        ];

        if (!count($this->categoryRepository->findAll())) {
            $categoriesCount = 5;

            for ($i = 0; $i < $categoriesCount; ++$i) {
                /**
                 * @var CategoryInterface $category
                 */
                $category = $this->categoryFactory->createNew();

                foreach (Tool::getValidLanguages() as $language) {
                    $category->setName($names[$i][$language] ?? $names[$i]['en'], $language);
                }

                $category->setParent($this->objectService->createFolderByPath('/demo/categories'));
                $category->setStores([$this->storeRepository->findStandard()->getId()]);
                $category->setPublished(true);
                $category->setKey($category->getName());
                $category->setKey(Service::getUniqueKey($category));
                $category->save();
            }
        }
    }
}
