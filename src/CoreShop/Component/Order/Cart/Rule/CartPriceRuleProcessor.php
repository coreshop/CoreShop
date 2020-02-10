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

namespace CoreShop\Component\Order\Cart\Rule;

use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeInterface;

class CartPriceRuleProcessor implements CartPriceRuleProcessorInterface
{
    /**
     * @var CartPriceRuleValidationProcessorInterface
     */
    private $cartPriceRuleValidator;

    /**
     * @var ProposalCartPriceRuleCalculatorInterface
     */
    private $proposalCartPriceRuleCalculator;

    /**
     * @param CartPriceRuleValidationProcessorInterface $cartPriceRuleValidator
     * @param ProposalCartPriceRuleCalculatorInterface  $proposalCartPriceRuleCalculator
     */
    public function __construct(
        CartPriceRuleValidationProcessorInterface $cartPriceRuleValidator,
        ProposalCartPriceRuleCalculatorInterface $proposalCartPriceRuleCalculator
    ) {
        $this->cartPriceRuleValidator = $cartPriceRuleValidator;
        $this->proposalCartPriceRuleCalculator = $proposalCartPriceRuleCalculator;
    }

    /**
     * {@inheritdoc}
     */
    public function process(OrderInterface $cart, CartPriceRuleInterface $cartPriceRule, CartPriceRuleVoucherCodeInterface $voucherCode = null): bool
    {
        if ($this->cartPriceRuleValidator->isValidCartRule($cart, $cartPriceRule, $voucherCode)) {
            $this->proposalCartPriceRuleCalculator->calculatePriceRule($cart, $cartPriceRule, $voucherCode);

            return true;
        }

        return false;
    }
}
