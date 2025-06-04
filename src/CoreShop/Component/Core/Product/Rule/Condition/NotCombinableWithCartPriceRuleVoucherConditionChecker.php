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

namespace CoreShop\Component\Core\Product\Rule\Condition;

use CoreShop\Component\Core\Model\OrderInterface;
use CoreShop\Component\Order\Model\PriceRuleItemInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Rule\Condition\ConditionCheckerInterface;
use CoreShop\Component\Rule\Model\RuleInterface;

final class NotCombinableWithCartPriceRuleVoucherConditionChecker implements ConditionCheckerInterface
{
    public function isValid(
        ResourceInterface $subject,
        RuleInterface $rule,
        array $configuration,
        array $params = [],
    ): bool {
        if (!isset($params['cart'])) {
            return true;
        }

        $cart = $params['cart'];

        if (!$cart instanceof OrderInterface) {
            return true;
        }

        $notCombinableIds = $configuration['price_rules'] ?? [];

        foreach ($cart->getPriceRuleItems()?->getItems() ?: [] as $cartRule) {
            if (!$cartRule instanceof PriceRuleItemInterface) {
                continue;
            }

            if (null === $cartRule->getCartPriceRule()) {
                continue;
            }

            if (in_array($cartRule->getCartPriceRule()->getId(), $notCombinableIds)) {
                return false;
            }
        }

        return true;
    }
}
