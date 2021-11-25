<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Core\Product\Cloner;

use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Core\Model\ProductStoreValuesInterface;
use Pimcore\Model\DataObject\ClassDefinition\Data\CustomDataCopyInterface;
use Pimcore\Model\DataObject\Concrete;

class ProductUnitDefinitionsCloner implements ProductClonerInterface
{
    public function __construct(protected UnitMatcherInterface $unitMatcher)
    {
    }

    public function clone(
        ProductInterface $product,
        ProductInterface $referenceProduct,
        bool $resetExistingData = false
    ): void {
        if ($product->hasUnitDefinitions() === true && $resetExistingData === false) {
            return;
        }

        /**
         * @var Concrete&ProductInterface $referenceProduct
         * @psalm-var Concrete&ProductInterface $referenceProduct
         */
        $unitDefinitionsFieldDefinition = $referenceProduct->getClass()->getFieldDefinition('unitDefinitions');

        if (!$unitDefinitionsFieldDefinition instanceof CustomDataCopyInterface) {
            throw new \Exception('Field Definition must implement CustomDataCopyInterface');
        }

        $storeValuesFieldDefinition = $referenceProduct->getClass()->getFieldDefinition('storeValues');

        if (!$storeValuesFieldDefinition instanceof CustomDataCopyInterface) {
            throw new \Exception('Field Definition must implement CustomDataCopyInterface');
        }

        $unitDefinitions = $unitDefinitionsFieldDefinition->createDataCopy(
            $referenceProduct,
            $referenceProduct->getUnitDefinitions()
        );

        $storeValues = $storeValuesFieldDefinition->createDataCopy(
            $referenceProduct,
            $referenceProduct->getStoreValues()
        );

        $product->setUnitDefinitions($unitDefinitions);
        $product->setStoreValues($storeValues);

        /**
         * @var ProductStoreValuesInterface $storeValue
         */
        foreach ($referenceProduct->getStoreValues() as $storeValue) {
            $newStoreValue = $product->getStoreValuesForStore($storeValue->getStore());

            if (!$newStoreValue) {
                continue;
            }

            foreach ($storeValue->getProductUnitDefinitionPrices() as $definitionPrice) {
                $newUnitDefinition = $this->unitMatcher->findMatchingUnitDefinitionByUnitName($product, $definitionPrice->getUnitDefinition()->getUnitName());

                if (!$newUnitDefinition) {
                    continue;
                }

                $newDefinitionPrice = clone $definitionPrice;

                $reflectionClass = new \ReflectionClass($newDefinitionPrice);
                $property = $reflectionClass->getProperty('id');
                $property->setAccessible(true);
                $property->setValue($newDefinitionPrice, null);

                $newDefinitionPrice->setProductStoreValues($newStoreValue);
                $newDefinitionPrice->setUnitDefinition($newUnitDefinition);

                $newStoreValue->addProductUnitDefinitionPrice($newDefinitionPrice);
            }
        }
    }
}
