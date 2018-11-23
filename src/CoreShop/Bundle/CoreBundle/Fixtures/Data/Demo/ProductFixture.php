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

use CoreShop\Bundle\FixtureBundle\Fixture\VersionedFixtureInterface;
use CoreShop\Component\Core\Model\CategoryInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Provider\Barcode;
use Faker\Provider\Image;
use Faker\Provider\Lorem;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\Service;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

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
            TaxRuleGroupFixture::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        /**
         * @var KernelInterface $kernel
         */
        $kernel = $this->container->get('kernel');

        if (!count($this->container->get('coreshop.repository.product')->findAll())) {
            $defaultStore = $this->container->get('coreshop.repository.store')->findStandard()->getId();
            $stores = $this->container->get('coreshop.repository.store')->findAll();

            $productsCount = 25;
            $faker = Factory::create();
            $faker->addProvider(new Lorem($faker));
            $faker->addProvider(new Barcode($faker));

            $categories = $this->container->get('coreshop.repository.category')->findAll();

            for ($i = 0; $i < $productsCount; $i++) {
                /**
                 * @var CategoryInterface $usedCategory
                 */
                $usedCategory = $categories[rand(0, count($categories) - 1)];
                $folder = \Pimcore\Model\Asset\Service::createFolderByPath(sprintf('/demo/products/%s', $usedCategory->getName()));

                $images = [];

                for ($j = 0; $j < 3; $j++) {
                    $imagePath = $kernel->locateResource(sprintf('@CoreShopCoreBundle/Resources/fixtures/image%s.jpeg', rand(1, 3)));

                    $fileName = 'image' . ($i) . '_' . ($j) . '.jpg';
                    $fullPath = $folder->getFullPath() . '/' . $fileName;

                    $existingImage = Asset::getByPath($fullPath);

                    if ($existingImage instanceof Asset) {
                        $existingImage->delete();
                    }

                    $image = new \Pimcore\Model\Asset\Image();
                    $image->setData(file_get_contents($imagePath));
                    $image->setParent($folder);
                    $image->setFilename($fileName);
                    \Pimcore\Model\Asset\Service::getUniqueKey($image);
                    $image->save();

                    $images[] = $image;
                }

                /**
                 * @var ProductInterface $product
                 */
                $product = $this->container->get('coreshop.factory.product')->createNew();
                $product->setName($faker->words(3, true));
                $product->setSku($faker->ean13);
                $product->setShortDescription($faker->text());
                $product->setDescription(implode('<br/>', $faker->paragraphs(3)));
                $product->setEan($faker->ean13);
                $product->setActive(true);
                $product->setCategories([$usedCategory]);
                $product->setOnHand(10);
                $product->setWholesalePrice($faker->randomFloat(2, 100, 200) * 100);

                foreach ($stores as $store) {
                    $product->setStorePrice(intval($faker->randomFloat(2, 200, 400)) * 100, $store);
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
