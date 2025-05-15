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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
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
