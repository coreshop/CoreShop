<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Fixtures\Data\Demo;

use CoreShop\Bundle\CoreBundle\Faker\Commerce;
use CoreShop\Bundle\FixtureBundle\Fixture\VersionedFixtureInterface;
use CoreShop\Component\Core\Model\CategoryInterface;
use CoreShop\Component\Pimcore\DataObject\ObjectServiceInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Pimcore\Model\DataObject\Service;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CategoryFixture extends AbstractFixture implements ContainerAwareInterface, VersionedFixtureInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        return '2.0';
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        if (!count($this->container->get('coreshop.repository.category')->findAll())) {
            $categoriesCount = 5;
            $faker = Factory::create();
            $faker->addProvider(new Commerce($faker));

            for ($i = 0; $i < $categoriesCount; $i++) {
                /**
                 * @var CategoryInterface $category
                 */
                $category = $this->container->get('coreshop.factory.category')->createNew();
                $category->setName($faker->department);
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
