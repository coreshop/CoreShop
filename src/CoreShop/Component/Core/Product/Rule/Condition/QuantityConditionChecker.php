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

use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Rule\Condition\ConditionCheckerInterface;
use CoreShop\Component\Rule\Model\RuleInterface;

final class QuantityConditionChecker implements ConditionCheckerInterface
{
    public function isValid(
        ResourceInterface $subject,
        RuleInterface $rule,
        array $configuration,
        array $params = [],
    ): bool {
        if (!array_key_exists('cart', $params) || !$params['cart'] instanceof OrderInterface) {
            return false;
        }

        $cart = $params['cart'];

        foreach ($cart->getItems() as $item) {
            if ($item instanceof OrderItemInterface) {
                if ($item->getProduct() instanceof ProductInterface) {
                    if ($item->getProduct()->getId() === $subject->getId()) {
                        return $item->getQuantity() >= $configuration['minQuantity'] && $item->getQuantity() <= $configuration['maxQuantity'];
                    }
                }
            }
        }

        return false;
    }
}
