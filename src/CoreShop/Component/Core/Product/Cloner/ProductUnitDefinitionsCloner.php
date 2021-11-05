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
use Doctrine\Common\Collections\ArrayCollection;

class ProductUnitDefinitionsCloner implements ProductClonerInterface
{
    /**
     * {@inheritDoc}
     */
    public function clone(ProductInterface $product, ProductInterface $referenceProduct, bool $resetExistingData = false)
    {
        if ($product->hasUnitDefinitions() === true && $resetExistingData === false) {
            return;
        }

        $unitDefinitions = clone $referenceProduct->getUnitDefinitions();

        //Hack to get rid of the ID
        $reflectionClass = new \ReflectionClass($unitDefinitions);
        $property = $reflectionClass->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($unitDefinitions, null);

        $property = $reflectionClass->getProperty('unitDefinitions');
        $property->setAccessible(true);
        $property->setValue($unitDefinitions, new ArrayCollection());

        $property = $reflectionClass->getProperty('product');
        $property->setAccessible(true);
        $property->setValue($unitDefinitions, null);

        $property = $reflectionClass->getProperty('defaultUnitDefinition');
        $property->setAccessible(true);
        $property->setValue($unitDefinitions, null);

        $newDefaultDefinition = clone $referenceProduct->getUnitDefinitions()->getDefaultUnitDefinition();
        $reflectionClass = new \ReflectionClass($newDefaultDefinition);
        $property = $reflectionClass->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($newDefaultDefinition, null);

        $unitDefinitions->setDefaultUnitDefinition($newDefaultDefinition);

        foreach ($referenceProduct->getUnitDefinitions()->getAdditionalUnitDefinitions() as $unitDefinition) {
            $newUnitDefinition = clone $unitDefinition;

            $reflectionClass = new \ReflectionClass($newUnitDefinition);
            $property = $reflectionClass->getProperty('id');
            $property->setAccessible(true);
            $property->setValue($newUnitDefinition, null);

            $newUnitDefinition->setProductUnitDefinitions($unitDefinitions);
            $unitDefinitions->addAdditionalUnitDefinition($newUnitDefinition);
        }

        $product->setUnitDefinitions($unitDefinitions);
    }
}
