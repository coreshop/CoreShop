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

use CoreShop\Bundle\FixtureBundle\Fixture\VersionedFixtureInterface;
use CoreShop\Component\Core\Model\CategoryInterface;
use CoreShop\Component\Pimcore\DataObject\ObjectServiceInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use Pimcore\Model\DataObject\Service;
use Pimcore\Tool;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CategoryFixture extends AbstractFixture implements ContainerAwareInterface, VersionedFixtureInterface
{
    private ?ContainerInterface $container;

    public function getVersion(): string
    {
        return '2.0';
    }

    public function setContainer(ContainerInterface $container = null): void
    {
        $this->container = $container;
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

        if (!count($this->container->get('coreshop.repository.category')->findAll())) {
            $categoriesCount = 5;

            for ($i = 0; $i < $categoriesCount; ++$i) {
                /**
                 * @var CategoryInterface $category
                 */
                $category = $this->container->get('coreshop.factory.category')->createNew();

                foreach (Tool::getValidLanguages() as $language) {
                    $category->setName($names[$i][$language] ?? $names[$i]['en'], $language);
                }

                $category->setParent($this->container->get(ObjectServiceInterface::class)->createFolderByPath('/demo/categories'));
                $category->setStores([$this->container->get('coreshop.repository.store')->findStandard()->getId()]);
                $category->setPublished(true);
                $category->setKey($category->getName());
                $category->setKey(Service::getUniqueKey($category));
                $category->save();
            }
        }
    }
}
