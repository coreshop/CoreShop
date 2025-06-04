<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Core\CartItem\Rule\Action;

use CoreShop\Component\Core\CartItem\Rule\Applier\CartItemRuleApplierInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Order\CartItem\Rule\Action\CartItemPriceRuleActionProcessorInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Order\Model\PriceRuleItemInterface;

class DiscountPercentActionProcessor implements CartItemPriceRuleActionProcessorInterface
{
    public function __construct(
        protected CartItemRuleApplierInterface $cartItemRuleApplier,
    ) {
    }

    public function applyRule(
        OrderItemInterface $orderItem,
        array $configuration,
        PriceRuleItemInterface $cartPriceRuleItem,
    ): bool {
        /**
         * @var StoreInterface $store
         */
        $store = $orderItem->getOrder()->getStore();

        $discount = $this->getDiscount($orderItem, $configuration, $store->getUseGrossPrice());

        if ($discount <= 0) {
            return false;
        }

        $this->cartItemRuleApplier->applyDiscount($orderItem, $cartPriceRuleItem, $discount, $store->getUseGrossPrice());

        return true;
    }

    public function unApplyRule(
        OrderItemInterface $orderItem,
        array $configuration,
        PriceRuleItemInterface $cartPriceRuleItem,
    ): bool {
        return true;
    }

    protected function getDiscount(OrderItemInterface $orderItem, array $configuration, $withTax = false): int
    {
        $total = $orderItem->getSubtotal($withTax);

        $amount = (int) round(($configuration['percent'] / 100) * $total);

        return $this->getApplicableAmount($amount, $amount);
    }

    protected function getApplicableAmount(int $cartAmount, int $ruleAmount): int
    {
        return min($cartAmount, $ruleAmount);
    }
}
