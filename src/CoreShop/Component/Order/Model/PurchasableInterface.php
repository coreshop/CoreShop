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

declare(strict_types=1);

namespace CoreShop\Component\Order\Model;

use CoreShop\Component\Currency\Model\Money;
use CoreShop\Component\Taxation\Model\TaxRuleGroupInterface;

interface PurchasableInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @param string|null $language
     *
     * @return string
     */
    public function getName($language = null);

    /**
     * @return int
     *
     * @deprecated getWholesalePrice is deprecated since 2.1.0 and will be removed with 2.2.0, use getWholesaleBuyingPrice instead
     */
    public function getWholesalePrice();

    /**
     * @return Money
     */
    public function getWholesaleBuyingPrice();

    /**
     * @return TaxRuleGroupInterface
     */
    public function getTaxRule();
}
