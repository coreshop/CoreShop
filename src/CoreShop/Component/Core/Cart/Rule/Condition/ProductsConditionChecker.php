<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Core\Cart\Rule\Condition;

use CoreShop\Component\Order\Cart\Rule\Condition\AbstractConditionChecker;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeInterface;
use CoreShop\Component\Product\Model\ProductInterface;

final class ProductsConditionChecker extends AbstractConditionChecker
{
    /**
     * {@inheritdoc}
     */
    public function isCartRuleValid(CartInterface $cart, CartPriceRuleInterface $cartPriceRule, ?CartPriceRuleVoucherCodeInterface $voucher, array $configuration)
    {
        foreach ($cart->getItems() as $item) {
            $product = $item->getProduct();

            if ($product instanceof ProductInterface) {
                if (in_array($product->getId(), $configuration['products'])) {
                    return true;
                }
            }
        }

        return false;
    }
}
