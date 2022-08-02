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

namespace CoreShop\Component\Core\Cart\Rule\Action;

use CoreShop\Component\Core\Cart\Rule\Applier\CartRuleApplierInterface;
use CoreShop\Component\Currency\Converter\CurrencyConverterInterface;
use CoreShop\Component\Order\Cart\Rule\Action\CartPriceRuleActionProcessorInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\ProposalCartPriceRuleItemInterface;
use CoreShop\Component\Order\Repository\CartPriceRuleVoucherRepositoryInterface;

class VoucherCreditActionProcessor implements CartPriceRuleActionProcessorInterface
{
    public function __construct(protected CurrencyConverterInterface $moneyConverter, protected CartRuleApplierInterface $cartRuleApplier, protected CartPriceRuleVoucherRepositoryInterface $voucherCodeRepository)
    {
    }

    public function applyRule(
        OrderInterface $cart,
        array $configuration,
        ProposalCartPriceRuleItemInterface $cartPriceRuleItem
    ): bool {
        if (!$cartPriceRuleItem->getVoucherCode()) {
            return false;
        }

        $voucherCode = $this->voucherCodeRepository->findByCode($cartPriceRuleItem->getVoucherCode());

        if (!$voucherCode) {
            return false;
        }

        if (!$voucherCode->getCreditCurrency() || !$voucherCode->isCreditCode()) {
            return false;
        }

        $discount = $this->getDiscount($cart, $voucherCode);
        $discount = $this->moneyConverter->convert(
            $discount,
            $voucherCode->getCreditCurrency()->getIsoCode(),
            $cart->getCurrency()->getIsoCode()
        );

        if ($discount <= 0) {
            return false;
        }

        $this->cartRuleApplier->applyDiscount($cart, $cartPriceRuleItem, $discount, true);

        return true;
    }

    public function unApplyRule(
        OrderInterface $cart,
        array $configuration,
        ProposalCartPriceRuleItemInterface $cartPriceRuleItem
    ): bool {
        return true;
    }

    protected function getDiscount(OrderInterface $cart, CartPriceRuleVoucherCodeInterface $voucherCode): int
    {
        return min($cart->getTotal(), $voucherCode->getCreditAvailable() - $voucherCode->getCreditUsed());
    }
}
