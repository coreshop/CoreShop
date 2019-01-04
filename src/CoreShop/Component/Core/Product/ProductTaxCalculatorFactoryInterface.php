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

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Order\Model\PurchasableInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;

interface ProductTaxCalculatorFactoryInterface
{
    /**
     * @param PurchasableInterface $product
     * @param AddressInterface     $address
     *
     * @return TaxCalculatorInterface
     */
    public function getTaxCalculator(PurchasableInterface $product, AddressInterface $address);
}
