<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Product\Calculator;

use CoreShop\Component\Product\Model\ProductInterface;

interface ProductPriceCalculatorInterface extends ProductRetailPriceCalculatorInterface, ProductDiscountPriceCalculatorInterface, ProductDiscountCalculatorInterface
{
    /**
     * @param ProductInterface $subject
     * @param bool             $withDiscount
     *
     * @return int
     */
    public function getPrice(ProductInterface $subject, $withDiscount = true);
}
