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

namespace CoreShop\Component\Core\Cart\Rule\Action;

use CoreShop\Component\Core\Cart\Rule\Applier\CartRuleApplierInterface;
use CoreShop\Component\Currency\Converter\CurrencyConverterInterface;
use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Currency\Repository\CurrencyRepositoryInterface;
use CoreShop\Component\Order\Cart\Rule\Action\CartPriceRuleActionProcessorInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Model\PriceRuleItemInterface;
use Webmozart\Assert\Assert;

class SurchargeAmountActionProcessor implements CartPriceRuleActionProcessorInterface
{
    public function __construct(
        protected CurrencyConverterInterface $moneyConverter,
        protected CurrencyRepositoryInterface $currencyRepository,
        protected CartRuleApplierInterface $cartRuleApplier,
    ) {
    }

    public function applyRule(
        OrderInterface $cart,
        array $configuration,
        PriceRuleItemInterface $cartPriceRuleItem,
    ): bool {
        $discount = $this->getDiscount($cart, $configuration);

        if ($discount <= 0) {
            return false;
        }

        $this->cartRuleApplier->applySurcharge($cart, $cartPriceRuleItem, $discount, $configuration['gross']);

        return true;
    }

    public function unApplyRule(
        OrderInterface $cart,
        array $configuration,
        PriceRuleItemInterface $cartPriceRuleItem,
    ): bool {
        return true;
    }

    protected function getDiscount(OrderInterface $cart, array $configuration): int
    {
        /**
         * @var CurrencyInterface $currency
         */
        $currency = $this->currencyRepository->find($configuration['currency']);
        $amount = $configuration['amount'];

        Assert::isInstanceOf($currency, CurrencyInterface::class);

        return $this->moneyConverter->convert(
            $amount,
            $currency->getIsoCode(),
            $cart->getCurrency()->getIsoCode(),
        );
    }
}
