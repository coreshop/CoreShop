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

namespace CoreShop\Bundle\CoreBundle\Fixtures\Data\Demo;

use CoreShop\Bundle\FixtureBundle\Fixture\VersionedFixtureInterface;
use CoreShop\Component\Core\Model\CategoryInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Model\ProductStoreValuesInterface;
use CoreShop\Component\Core\Model\ProductUnitDefinitionPriceInterface;
use CoreShop\Component\Product\Model\ProductUnitDefinitionInterface;
use CoreShop\Component\Product\Model\ProductUnitDefinitionsInterface;
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

class ProductWithUnitFixture extends AbstractProductFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $stores = $this->container->get('coreshop.repository.store')->findAll();

        $productsCount = 10;
        $decimalFactor = $this->container->getParameter('coreshop.currency.decimal_factor');
        $faker = Factory::create();
        $faker->addProvider(new Lorem($faker));
        $faker->addProvider(new Barcode($faker));

        for ($i = 0; $i < $productsCount; $i++) {
            $product = $this->createProduct('products-with-unit');

            $productUnitDefinitionFactory = $this->container->get('coreshop.factory.product_unit_definition');
            $productUnitDefinitionsFactory = $this->container->get('coreshop.factory.product_unit_definitions');
            $productUnitDefinitionPriceFactory = $this->container->get('coreshop.factory.product_unit_definition_price');
            $storeValuesFactory = $this->container->get('coreshop.factory.product_store_values');

            /**
             * @var ProductUnitDefinitionsInterface $unitDefinitions
             */
            $unitDefinitions = $productUnitDefinitionsFactory->createNew();
            $unitDefinitions->setProduct($product);

            /**
             * @var ProductUnitDefinitionInterface      $defaultDefinition
             * @var ProductUnitDefinitionInterface      $cartonDefinition
             * @var ProductUnitDefinitionInterface      $paletteDefinition
             * @var ProductUnitDefinitionPriceInterface $cartonPrice
             * @var ProductUnitDefinitionPriceInterface $palettePrice
             */
            $defaultDefinition = $productUnitDefinitionFactory->createNew();
            $defaultDefinition->setUnit($this->getReference('unit-piece'));

            $cartonDefinition = $productUnitDefinitionFactory->createNew();
            $cartonDefinition->setUnit($this->getReference('unit-carton'));
            $cartonDefinition->setConversionRate(24);

            $paletteDefinition = $productUnitDefinitionFactory->createNew();
            $paletteDefinition->setUnit($this->getReference('unit-palette'));
            $paletteDefinition->setConversionRate(24 * 40);

            $unitDefinitions->setDefaultUnitDefinition($defaultDefinition);
            $unitDefinitions->addAdditionalUnitDefinition($cartonDefinition);
            $unitDefinitions->addAdditionalUnitDefinition($paletteDefinition);

            $product->setUnitDefinitions($unitDefinitions);

            foreach ($stores as $store) {
                /**
                 * @var ProductStoreValuesInterface $storeValues
                 */
                $storeValues = $product->getStoreValues($store);

                if (null === $storeValues) {
                    $storeValues = $storeValuesFactory->createNew();
                    $storeValues->setStore($store);
                }

                $storeValues->setPrice((int) $faker->randomFloat(2, 200, 400) * $decimalFactor);

                $cartonPrice = $productUnitDefinitionPriceFactory->createNew();
                $cartonPrice->setUnitDefinition($cartonDefinition);
                $cartonPrice->setPrice($product->getStorePrice($store) * 20);

                $palettePrice = $productUnitDefinitionPriceFactory->createNew();
                $palettePrice->setPrice($product->getStorePrice($store) * 20 * 38);
                $palettePrice->setUnitDefinition($paletteDefinition);

                $storeValues->addProductUnitDefinitionPrice($cartonPrice);
                $storeValues->addProductUnitDefinitionPrice($palettePrice);

                $product->setStoreValues($storeValues, $store);
            }

            $product->save();
        }
    }
}
