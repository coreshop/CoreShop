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

namespace CoreShop\Bundle\ProductBundle\DependencyInjection\Compiler;

final class ProductRetailPriceCalculatorsPass extends AbstractProductPriceCalculatorPass
{
    public const PRODUCT_RETAIL_PRICE_CALCULATOR_TAG = 'coreshop.product.retail_price_calculator';

    /**
     * {@inheritdoc}
     */
    protected function getRegistry()
    {
        return 'coreshop.registry.product.retail_price_calculators';
    }

    /**
     * {@inheritdoc}
     */
    protected function getTag()
    {
        return self::PRODUCT_RETAIL_PRICE_CALCULATOR_TAG;
    }

    /**
     * {@inheritdoc}
     */
    protected function getParameter()
    {
        return 'coreshop.product.retail_price_calculators';
    }
}
