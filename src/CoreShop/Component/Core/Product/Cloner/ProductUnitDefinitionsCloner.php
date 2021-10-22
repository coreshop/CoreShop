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

declare(strict_types=1);

namespace CoreShop\Component\Core\Product\Cloner;

use CoreShop\Component\Core\Model\ProductInterface;

class ProductUnitDefinitionsCloner implements ProductClonerInterface
{
    public function clone(ProductInterface $product, ProductInterface $referenceProduct, bool $resetExistingData = false): void
    {
        if ($resetExistingData === false && $product->hasUnitDefinitions() === true) {
            return;
        }

        $unitDefinitions = $referenceProduct->getUnitDefinitions();

        if (null === $unitDefinitions) {
            return;
        }

        $newUnitDefinitions = clone $unitDefinitions;

        //Hack to get rid of the ID
        $reflectionClass = new \ReflectionClass($newUnitDefinitions);
        $property = $reflectionClass->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($unitDefinitions, null);

        foreach ($newUnitDefinitions->getUnitDefinitions() as $unitDefinition) {
            $reflectionClass = new \ReflectionClass($unitDefinition);
            $property = $reflectionClass->getProperty('id');
            $property->setAccessible(true);
            $property->setValue($unitDefinition, null);
        }

        $product->setUnitDefinitions($newUnitDefinitions);
    }
}
