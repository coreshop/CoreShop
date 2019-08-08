<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
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
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

abstract class AbstractProductFixture extends AbstractFixture implements ContainerAwareInterface, VersionedFixtureInterface, DependentFixtureInterface
{
    use ContainerAwareTrait;

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
    public function getDependencies()
    {
        return [
            CategoryFixture::class,
            TaxRuleGroupFixture::class,
        ];
    }

    /**
     * @param string $parentPath
     * @return ProductInterface
     * @throws \Exception
     */
    protected function createProduct(string $parentPath)
    {
        $faker = Factory::create();
        $faker->addProvider(new Lorem($faker));
        $faker->addProvider(new Barcode($faker));
        $decimalFactor = $this->container->getParameter('coreshop.currency.decimal_factor');

        $defaultStore = $this->container->get('coreshop.repository.store')->findStandard()->getId();
        $stores = $this->container->get('coreshop.repository.store')->findAll();

        /**
         * @var KernelInterface $kernel
         */
        $kernel = $this->container->get('kernel');
        $categories = $this->container->get('coreshop.repository.category')->findAll();

        /**
         * @var CategoryInterface $usedCategory
         */
        $usedCategory = $categories[rand(0, count($categories) - 1)];
        $folder = \Pimcore\Model\Asset\Service::createFolderByPath(sprintf('/demo/%s/%s', $parentPath, $usedCategory->getName()));

        $images = [];

        for ($j = 0; $j < 3; $j++) {
            $imagePath = $kernel->locateResource(sprintf('@CoreShopCoreBundle/Resources/fixtures/image%s.jpeg', rand(1, 3)));

            $fileName = sprintf('image_%s.jpg', uniqid());
            $fullPath = $folder->getFullPath() . '/' . $fileName;

            $existingImage = Asset::getByPath($fullPath);

            if ($existingImage instanceof Asset) {
                $existingImage->delete();
            }

            $image = new \Pimcore\Model\Asset\Image();
            $image->setData(file_get_contents($imagePath));
            $image->setParent($folder);
            $image->setFilename($fileName);
            $image->setFilename(\Pimcore\Model\Asset\Service::getUniqueKey($image));
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
        $product->setWholesalePrice($faker->randomFloat(2, 100, 200) * $decimalFactor);

        foreach ($stores as $store) {
            $product->setStorePrice((int) $faker->randomFloat(2, 200, 400) * $decimalFactor, $store);
        }

        $product->setTaxRule($this->getReference('taxRule'));
        $product->setWidth($faker->numberBetween(5, 10));
        $product->setHeight($faker->numberBetween(5, 10));
        $product->setDepth($faker->numberBetween(5, 10));
        $product->setWeight($faker->numberBetween(5, 10));
        $product->setImages($images);
        $product->setStores([$defaultStore]);
        $product->setParent($this->container->get('coreshop.object_service')->createFolderByPath(sprintf('/demo/%s/%s', $parentPath, $usedCategory->getName())));
        $product->setKey($product->getName());
        $product->setPublished(true);
        $product->setKey(Service::getUniqueKey($product));

        return $product;
    }
}
