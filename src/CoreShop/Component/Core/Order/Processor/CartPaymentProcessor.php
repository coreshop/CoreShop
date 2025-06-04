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

namespace CoreShop\Component\Core\Order\Processor;

use CoreShop\Component\Order\Cart\CartContextResolverInterface;
use CoreShop\Component\Order\Factory\AdjustmentFactoryInterface;
use CoreShop\Component\Order\Model\AdjustmentInterface;
use CoreShop\Component\Order\Model\OrderInterface;
use CoreShop\Component\Order\Processor\CartProcessorInterface;
use CoreShop\Component\Payment\Calculator\PaymentProviderRulePriceCalculatorInterface;
use CoreShop\Component\Payment\Checker\PaymentProviderRuleCheckerInterface;
use Pimcore\Translation\Translator;

final class CartPaymentProcessor implements CartProcessorInterface
{
    public function __construct(
        private int $decimalFactor,
        private int $decimalPrecision,
        protected PaymentProviderRulePriceCalculatorInterface $priceCalculator,
        private CartContextResolverInterface $cartContextResolver,
        private AdjustmentFactoryInterface $adjustmentFactory,
        protected PaymentProviderRuleCheckerInterface $paymentProviderRuleChecker,
        protected Translator $translator,
    ) {
    }

    public function process(OrderInterface $cart): void
    {
        $cart->setPaymentTotal(
            (int) round((round($cart->getTotal() / $this->decimalFactor, $this->decimalPrecision) * 100), 0),
        );
        $paymentProvider = $cart->getPaymentProvider();

        if ($cart->isImmutable()) {
            return;
        }

        if (
            $paymentProvider &&
            $validRule = $this->paymentProviderRuleChecker->findValidPaymentProviderRule($paymentProvider, $cart)
        ) {
            $context = $this->cartContextResolver->resolveCartContext($cart);

            $price = $this->priceCalculator->getPrice(
                $paymentProvider,
                $cart,
                $context,
            );

            $ruleLabel = $validRule->getLabel($cart->getLocaleCode());
            $defaultRuleLabel = $this->translator->trans('coreshop.paymentprovider.rule.label');

            $cart->addAdjustment(
                $this->adjustmentFactory->createWithData(
                    AdjustmentInterface::PAYMENT,
                    !empty($ruleLabel) ? $ruleLabel : $defaultRuleLabel,
                    $price,
                    $price,
                ),
            );
        }
    }
}
