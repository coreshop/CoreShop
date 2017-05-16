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

namespace CoreShop\Component\Order\Model;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;

interface PurchasableInterface
{
    /**
     * @param string|null $language
     * @return string
     */
    public function getName($language = null);

    /**
     * @param bool $withTax
     *
     * @return float
     */
    public function getPrice($withTax = true);

    /**
     * @param bool $withTax
     *
     * @return mixed
     */
    public function getBasePrice($withTax = true);

    /**
     * @return mixed
     */
    public function getWholesalePrice();

    /**
     * @param AddressInterface|null $address
     *
     * @return TaxCalculatorInterface
     */
    public function getTaxCalculator(AddressInterface $address = null);
}