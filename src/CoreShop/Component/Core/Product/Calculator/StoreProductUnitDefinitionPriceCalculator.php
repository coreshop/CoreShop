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

namespace CoreShop\Component\Core\Product\Calculator;

use CoreShop\Component\Core\Model\ProductStoreValuesInterface;
use CoreShop\Component\Core\Model\ProductUnitDefinitionPriceInterface;
use CoreShop\Component\Product\Calculator\ProductRetailPriceCalculatorInterface;
use CoreShop\Component\Product\Exception\NoRetailPriceFoundException;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Product\Model\ProductUnitDefinitionInterface;
use CoreShop\Component\Store\Model\StoreInterface;
use Webmozart\Assert\Assert;

final class StoreProductUnitDefinitionPriceCalculator implements ProductRetailPriceCalculatorInterface
{
    public function getRetailPrice(ProductInterface $product, array $context): int
    {
        /**
         * @var \CoreShop\Component\Core\Model\ProductInterface $product
         */
        Assert::isInstanceOf($product, \CoreShop\Component\Core\Model\ProductInterface::class);
        Assert::keyExists($context, 'store');
        Assert::isInstanceOf($context['store'], StoreInterface::class);

        if (!isset($context['unitDefinition']) || !$context['unitDefinition'] instanceof ProductUnitDefinitionInterface) {
            throw new NoRetailPriceFoundException(__CLASS__);
        }

        $contextUnitDefinition = $context['unitDefinition'];
        $contextStore = $context['store'];

        $storeValues = $product->getStoreValuesForStore($contextStore);
        if (!$storeValues instanceof ProductStoreValuesInterface) {
            throw new NoRetailPriceFoundException(__CLASS__);
        }

        $unitDefinitionPrices = $storeValues->getProductUnitDefinitionPrices();

        if ($unitDefinitionPrices->count() === 0) {
            throw new NoRetailPriceFoundException(__CLASS__);
        }

        $filteredDefinitionPrices = $unitDefinitionPrices->filter(function (ProductUnitDefinitionPriceInterface $unitDefinitionPrice) use ($contextUnitDefinition) {
            return $unitDefinitionPrice->getUnitDefinition()->getId() === $contextUnitDefinition->getId();
        });

        if ($filteredDefinitionPrices->count() === 0) {
            throw new NoRetailPriceFoundException(__CLASS__);
        }

        $first = $filteredDefinitionPrices->first();

        if (false === $first) {
            throw new NoRetailPriceFoundException(__CLASS__);
        }

        return $first->getPrice();
    }
}
