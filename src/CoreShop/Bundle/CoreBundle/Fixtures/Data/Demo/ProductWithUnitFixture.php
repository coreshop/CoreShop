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

use CoreShop\Component\Core\Model\ProductStoreValuesInterface;
use CoreShop\Component\Core\Model\ProductUnitDefinitionPriceInterface;
use CoreShop\Component\Product\Model\ProductUnitDefinitionInterface;
use CoreShop\Component\Product\Model\ProductUnitDefinitionsInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Provider\Barcode;
use Faker\Provider\Lorem;

class ProductWithUnitFixture extends AbstractProductFixture
{
    public function load(ObjectManager $manager): void
    {
        $stores = $this->container->get('coreshop.repository.store')->findAll();

        $productsCount = 10;
        $decimalFactor = $this->container->getParameter('coreshop.currency.decimal_factor');
        $faker = Factory::create();
        $faker->addProvider(new Lorem($faker));
        $faker->addProvider(new Barcode($faker));

        for ($i = 0; $i < $productsCount; ++$i) {
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
             * @var ProductUnitDefinitionInterface $defaultDefinition
             */
            $defaultDefinition = $productUnitDefinitionFactory->createNew();
            $defaultDefinition->setUnit($this->getReference('unit-piece'));

            /**
             * @var ProductUnitDefinitionInterface $cartonDefinition
             */
            $cartonDefinition = $productUnitDefinitionFactory->createNew();
            $cartonDefinition->setUnit($this->getReference('unit-carton'));
            $cartonDefinition->setConversionRate(24);

            /**
             * @var ProductUnitDefinitionInterface $paletteDefinition
             */
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
                $storeValues = $product->getStoreValuesForStore($store);

                if (null === $storeValues) {
                    $storeValues = $storeValuesFactory->createNew();
                    $storeValues->setStore($store);
                }

                $storeValues->setPrice((int) $faker->randomFloat(2, 200, 400) * $decimalFactor);

                /**
                 * @var ProductUnitDefinitionPriceInterface $cartonPrice
                 */
                $cartonPrice = $productUnitDefinitionPriceFactory->createNew();
                $cartonPrice->setUnitDefinition($cartonDefinition);
                $cartonPrice->setPrice($product->getStoreValuesOfType('price', $store) * 20);

                /**
                 * @var ProductUnitDefinitionPriceInterface $palettePrice
                 */
                $palettePrice = $productUnitDefinitionPriceFactory->createNew();
                $palettePrice->setPrice($product->getStoreValuesOfType('price', $store) * 20 * 38);
                $palettePrice->setUnitDefinition($paletteDefinition);

                $storeValues->addProductUnitDefinitionPrice($cartonPrice);
                $storeValues->addProductUnitDefinitionPrice($palettePrice);

                $product->setStoreValuesForStore($storeValues, $store);
            }

            $product->save();
        }
    }
}
