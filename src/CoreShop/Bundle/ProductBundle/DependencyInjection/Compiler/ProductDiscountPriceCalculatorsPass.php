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

namespace CoreShop\Bundle\ProductBundle\DependencyInjection\Compiler;

use CoreShop\Component\Registry\RegisterSimpleRegistryTypePass;

final class ProductDiscountPriceCalculatorsPass extends RegisterSimpleRegistryTypePass
{
    public const PRODUCT_DISCOUNT_PRICE_CALCULATOR_TAG = 'coreshop.product.discount_price_calculator';

    public function __construct()
    {
        parent::__construct(
            'coreshop.registry.product.discount_price_calculators',
            'coreshop.product.discount_price_calculators',
            self::PRODUCT_DISCOUNT_PRICE_CALCULATOR_TAG
        );
    }
}
