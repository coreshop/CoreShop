<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle\Fixtures\Data\Demo;

use CoreShop\Component\Core\Model\CategoryInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Bundle\FixtureBundle\Fixture\VersionedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Provider\Barcode;
use Faker\Provider\Image;
use Faker\Provider\Lorem;
use Pimcore\Model\DataObject\Service;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ProductFixture extends AbstractFixture implements ContainerAwareInterface, VersionedFixtureInterface, DependentFixtureInterface
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
    public function getDependencies()
    {
        return [
            CategoryFixture::class,
            TaxRuleGroupFixture::class
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        if (!count($this->container->get('coreshop.repository.product')->findAll())) {
            $defaultStore = $this->container->get('coreshop.repository.store')->findStandard()->getId();
            $stores = $this->container->get('coreshop.repository.store')->findAll();

            $productsCount = 25;
            $faker = Factory::create();
            $faker->addProvider(new Lorem($faker));
            $faker->addProvider(new Barcode($faker));
            $faker->addProvider(new Image($faker));

            $categories = $this->container->get('coreshop.repository.category')->findAll();

            for ($i = 0; $i < $productsCount; $i++) {
                /**
                 * @var $usedCategory CategoryInterface
                 */
                $usedCategory = $categories[rand(0, count($categories) - 1)];

                $images = [];

                for ($j = 0; $j < 3; $j++) {
                    $image = new \Pimcore\Model\Asset\Image();
                    $image->setData(file_get_contents($faker->imageUrl(1000, 1000, 'technics')));
                    $image->setParent(\Pimcore\Model\Asset\Service::createFolderByPath(sprintf('/demo/products/%s', $usedCategory->getName())));
                    $image->setFilename('image' . ($i) . '_' . ($j) . '.jpg');
                    \Pimcore\Model\Asset\Service::getUniqueKey($image);
                    $image->save();

                    $images[] = $image;
                }

                /**
                 * @var $product ProductInterface
                 */
                $product = $this->container->get('coreshop.factory.product')->createNew();
                $product->setName($faker->words(3, true));
                $product->setSku($faker->ean13);
                $product->setShortDescription($faker->text());
                $product->setDescription(implode("<br/>", $faker->paragraphs(3)));
                $product->setEan($faker->ean13);
                $product->setActive(true);
                $product->setCategories([$usedCategory]);
                $product->setOnHand(10);
                $product->setWholesalePrice($faker->randomFloat(2, 100, 200) * 100);

                foreach ($stores as $store) {
                    $product->setStorePrice($faker->randomFloat(2, 200, 400) * 100, $store);
                }

                $product->setTaxRule($this->getReference('taxRule'));
                $product->setWidth($faker->numberBetween(5, 10));
                $product->setHeight($faker->numberBetween(5, 10));
                $product->setDepth($faker->numberBetween(5, 10));
                $product->setWeight($faker->numberBetween(5, 10));
                $product->setImages($images);
                $product->setStores([$defaultStore]);
                $product->setParent($this->container->get('coreshop.object_service')->createFolderByPath(sprintf('/demo/products/%s', $usedCategory->getName())));
                $product->setKey($product->getName());
                $product->setPublished(true);

                Service::getUniqueKey($product);

                $product->save();
            }
        }
    }
}
