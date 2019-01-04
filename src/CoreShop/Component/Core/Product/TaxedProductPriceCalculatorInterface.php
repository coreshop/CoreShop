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

namespace CoreShop\Component\Core\Product;

use CoreShop\Component\Order\Model\PurchasableInterface;

interface TaxedProductPriceCalculatorInterface
{
    /**
     * @param PurchasableInterface $product
     * @param array                $context
     * @param bool                 $withTax
     *
     * @return int
     */
    public function getPrice(PurchasableInterface $product, array $context, $withTax = true);

    /**
     * @param PurchasableInterface $product
     * @param array                $context
     * @param bool                 $withTax
     *
     * @return int
     */
    public function getDiscountPrice(PurchasableInterface $product, array $context, $withTax = true);

    /**
     * @param PurchasableInterface $product
     * @param array                $context
     * @param bool                 $withTax
     *
     * @return int
     */
    public function getDiscount(PurchasableInterface $product, array $context, $withTax = true);

    /**
     * @param PurchasableInterface $product
     * @param array                $context
     * @param bool                 $withTax
     *
     * @return int
     */
    public function getRetailPrice(PurchasableInterface $product, array $context, $withTax = true);
}
