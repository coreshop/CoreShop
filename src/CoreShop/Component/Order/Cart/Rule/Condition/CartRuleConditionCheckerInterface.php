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

namespace CoreShop\Component\Order\Cart\Rule\Condition;

use CoreShop\Component\Address\Model\AddressInterface;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeInterface;
use CoreShop\Component\Rule\Condition\ConditionCheckerInterface;
use CoreShop\Component\Shipping\Model\CarrierInterface;
use CoreShop\Component\Shipping\Model\ShippableInterface;

interface CartRuleConditionCheckerInterface extends ConditionCheckerInterface
{
    /**
     * @param CartInterface $cart
     * @param CartPriceRuleVoucherCodeInterface $voucher
     * @param array $configuration
     *
     * @return boolean
     */
    public function isCartRuleValid(CartInterface $cart, CartPriceRuleVoucherCodeInterface $voucher, array $configuration);
}
