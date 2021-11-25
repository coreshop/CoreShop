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
use CoreShop\Component\Product\Model\ProductUnitDefinitionInterface;

class UnitMatcher implements UnitMatcherInterface
{
    public function findMatchingUnitDefinitionByUnitName(ProductInterface $product, string $unitName)
    {
        if ($product->hasUnitDefinitions() === false) {
            return null;
        }

        foreach ($product->getUnitDefinitions()->getUnitDefinitions() as $unitDefinition) {

            if (!$unitDefinition instanceof ProductUnitDefinitionInterface) {
                continue;
            }

            if ($unitDefinition->getUnitName() === $unitName) {
                return $unitDefinition;
            }
        }

        return null;
    }
}
