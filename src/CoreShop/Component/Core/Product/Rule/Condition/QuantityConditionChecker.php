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

namespace CoreShop\Component\Core\Product\Rule\Condition;

use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\OrderItemInterface;
use CoreShop\Component\Product\Model\ProductInterface;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Rule\Condition\ConditionCheckerInterface;
use CoreShop\Component\Rule\Model\RuleInterface;

final class QuantityConditionChecker implements ConditionCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isValid(
        ResourceInterface $subject,
        RuleInterface $rule,
        array $configuration,
        array $params = []
    ): bool {
        if (array_key_exists('cart', $params) || !$params['cart'] instanceof OrderInterface) {
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
