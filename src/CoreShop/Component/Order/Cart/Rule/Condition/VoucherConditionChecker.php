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
use CoreShop\Component\Order\Repository\CartPriceRuleVoucherRepositoryInterface;

class VoucherConditionChecker extends AbstractConditionChecker
{
    public function __construct(
        private CartPriceRuleVoucherRepositoryInterface $voucherCodeRepository,
    ) {
    }

    public function isCartRuleValid(OrderInterface $cart, CartPriceRuleInterface $cartPriceRule, ?CartPriceRuleVoucherCodeInterface $voucher, array $configuration): bool
    {
        if (null === $voucher) {
            return false;
        }

        $maxUsagePerCode = $configuration['maxUsagePerCode'];
        $onlyOnePerCart = $configuration['onlyOnePerCart'];

        $storedCode = $this->voucherCodeRepository->findByCode($voucher->getCode());

        if (!$storedCode instanceof CartPriceRuleVoucherCodeInterface) {
            return false;
        }

        // max usage per code condition
        if (is_numeric($maxUsagePerCode)) {
            if ($maxUsagePerCode != 0 && $storedCode->getUses() >= $maxUsagePerCode) {
                return false;
            }
        }

        // only once per cart condition
        if ($onlyOnePerCart === true) {
            $valid = true;
            if ($cart->hasPriceRules()) {
                foreach ($cart->getPriceRuleItems() as $rule) {
                    if ($rule instanceof PriceRuleItemInterface) {
                        if ($rule->getCartPriceRule()->getIsVoucherRule() && $rule->getVoucherCode() !== $storedCode->getCode()) {
                            $valid = false;

                            break;
                        }
                    }
                }
            }

            return $valid;
        }

        return true;
    }
}
