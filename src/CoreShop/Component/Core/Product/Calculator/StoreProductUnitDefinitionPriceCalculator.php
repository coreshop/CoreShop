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
        /*
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

        if (0 === $unitDefinitionPrices->count()) {
            throw new NoRetailPriceFoundException(__CLASS__);
        }

        $filteredDefinitionPrices = $unitDefinitionPrices->filter(function (ProductUnitDefinitionPriceInterface $unitDefinitionPrice) use ($contextUnitDefinition) {
            return $unitDefinitionPrice->getUnitDefinition()->getId() === $contextUnitDefinition->getId();
        });

        if (0 === $filteredDefinitionPrices->count()) {
            throw new NoRetailPriceFoundException(__CLASS__);
        }

        $first = $filteredDefinitionPrices->first();

        if (false === $first) {
            throw new NoRetailPriceFoundException(__CLASS__);
        }

        return $first->getPrice();
    }
}
