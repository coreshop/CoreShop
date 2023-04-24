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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Order\Cart\Rule\Condition;

use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\PriceRuleItemInterface;

class NotCombinableConditionChecker extends AbstractConditionChecker
{
    public function isCartRuleValid(
        OrderInterface $cart,
        CartPriceRuleInterface $cartPriceRule,
        ?CartPriceRuleVoucherCodeInterface $voucher,
        array $configuration,
    ): bool {
        if (!$cart->hasPriceRules()) {
            return true;
        }

        $notCombinableIds = $configuration['price_rules'] ?? [];

        foreach ($cart->getPriceRuleItems() as $rule) {
            if (!$rule instanceof PriceRuleItemInterface) {
                continue;
            }

            if (in_array($rule->getCartPriceRule()->getId(), $notCombinableIds)) {
                return false;
            }
        }

        return true;
    }
}
