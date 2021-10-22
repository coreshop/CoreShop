<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Order\Cart\Rule;

use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeInterface;
use CoreShop\Component\Order\Model\OrderInterface;

class CartPriceRuleProcessor implements CartPriceRuleProcessorInterface
{
    public function __construct(private CartPriceRuleValidationProcessorInterface $cartPriceRuleValidator, private ProposalCartPriceRuleCalculatorInterface $proposalCartPriceRuleCalculator)
    {
    }

    public function process(OrderInterface $cart, CartPriceRuleInterface $cartPriceRule, CartPriceRuleVoucherCodeInterface $voucherCode = null): bool
    {
        if ($this->cartPriceRuleValidator->isValidCartRule($cart, $cartPriceRule, $voucherCode)) {
            $this->proposalCartPriceRuleCalculator->calculatePriceRule($cart, $cartPriceRule, $voucherCode);

            return true;
        }

        return false;
    }
}
