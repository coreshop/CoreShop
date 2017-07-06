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
use CoreShop\Component\Core\Model\TaxRuleGroupInterface;
use CoreShop\Component\Taxation\Calculator\TaxCalculatorInterface;

interface PurchasableInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @param string|null $language
     * @return string
     */
    public function getName($language = null);

    /**
     * @param bool $withTax
     * @return int
     */
    public function getPrice($withTax = true);

    /**
     * @return int
     */
    public function getBasePrice();

    /**
     * @return int
     */
    public function getWholesalePrice();

    /**
     * @return TaxRuleGroupInterface
     */
    public function getTaxRule();
}