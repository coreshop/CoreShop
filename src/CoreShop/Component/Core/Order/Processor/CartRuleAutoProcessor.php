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

namespace CoreShop\Component\Core\Order\Processor;

use CoreShop\Component\Order\Cart\Rule\CartPriceRuleProcessorInterface;
use CoreShop\Component\Order\Cart\Rule\CartPriceRuleUnProcessorInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\PriceRuleItemInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;
use CoreShop\Component\Order\Repository\CartPriceRuleRepositoryInterface;

final class CartRuleAutoProcessor implements CartProcessorInterface
{
    public function __construct(
        private CartPriceRuleRepositoryInterface $cartPriceRuleRepository,
        private CartPriceRuleProcessorInterface $cartPriceRuleProcessor,
        private CartPriceRuleUnProcessorInterface $cartPriceRuleUnProcessor,
    ) {
    }

    public function process(OrderInterface $cart): void
    {
        $eligibleRules = $this->cartPriceRuleRepository->findNonVoucherRules();

        /**
         * Remove all Rules to properly re-calculate them later
         *
         * We have to remove them again, since the priority and possible
         * un-combinable rules might be affected and cause other rules
         * to be valid or invalid.
         */
        $existingItems = $cart->getPriceRuleItems()?->getItems() ?? [];
        foreach ($existingItems as $priceRuleItem) {
            if (!$priceRuleItem instanceof PriceRuleItemInterface) {
                continue;
            }

            if (null !== $priceRuleItem->getVoucherCode()) {
                continue;
            }

            $this->cartPriceRuleUnProcessor->unProcess($cart, $priceRuleItem->getCartPriceRule());
        }

        foreach ($eligibleRules as $eligibleRule) {
            if (!$this->cartPriceRuleProcessor->process($cart, $eligibleRule)) {
                $this->cartPriceRuleUnProcessor->unProcess($cart, $eligibleRule);
            }
        }
    }
}
