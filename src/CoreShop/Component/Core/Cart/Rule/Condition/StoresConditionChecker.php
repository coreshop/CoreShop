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

namespace CoreShop\Component\Core\Cart\Rule\Condition;

use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Order\Cart\Rule\Condition\AbstractConditionChecker;
use CoreShop\Component\Order\Model\CartInterface;
use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeInterface;
use Webmozart\Assert\Assert;

final class StoresConditionChecker extends AbstractConditionChecker
{
    /**
     * {@inheritdoc}
     */
    public function isCartRuleValid(CartInterface $cart, CartPriceRuleInterface $cartPriceRule, CartPriceRuleVoucherCodeInterface $voucher = null, array $configuration)
    {
        /*
         * @var $cart \CoreShop\Component\Core\Model\CartInterface
         */
        Assert::isInstanceOf($cart, \CoreShop\Component\Core\Model\CartInterface::class);

        if (!$cart->getStore() instanceof StoreInterface) {
            return false;
        }

        return in_array($cart->getStore()->getId(), $configuration['stores']);
    }
}
