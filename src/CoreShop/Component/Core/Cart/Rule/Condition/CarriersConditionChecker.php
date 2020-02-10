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

namespace CoreShop\Component\Core\Cart\Rule\Condition;

use CoreShop\Component\Core\Model\CarrierInterface;
use CoreShop\Component\Order\Cart\Rule\Condition\AbstractConditionChecker;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeInterface;
use Webmozart\Assert\Assert;

final class CarriersConditionChecker extends AbstractConditionChecker
{
    /**
     * {@inheritdoc}
     */
    public function isCartRuleValid(OrderInterface $cart, CartPriceRuleInterface $cartPriceRule, ?CartPriceRuleVoucherCodeInterface $voucher, array $configuration): bool
    {
        /**
         * @var $cart \CoreShop\Component\Core\Model\OrderInterface
         */
        Assert::isInstanceOf($cart, \CoreShop\Component\Core\Model\OrderInterface::class);

        if (!$cart->getCarrier() instanceof CarrierInterface) {
            return false;
        }

        return in_array($cart->getCarrier()->getId(), $configuration['carriers']);
    }
}
