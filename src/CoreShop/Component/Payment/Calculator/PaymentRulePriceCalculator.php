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

namespace CoreShop\Component\Payment\Calculator;

use CoreShop\Component\Payment\Checker\PaymentRuleCheckerInterface;
use CoreShop\Component\Payment\Model\PayableInterface;
use CoreShop\Component\Payment\Model\PaymentProviderInterface;
use CoreShop\Component\Payment\Model\PaymentRuleInterface;
use CoreShop\Component\Payment\Rule\Processor\PaymentRuleActionProcessorInterface;

class PaymentRulePriceCalculator
{
    public function __construct(
        protected PaymentRuleCheckerInterface $paymentRuleChecker,
        protected PaymentRuleActionProcessorInterface $paymentRuleProcessor,
    ) {
    }

    public function getPrice(PaymentProviderInterface $paymentProvider, PayableInterface $payable, array $context): int
    {
        /**
         * First valid price rule wins. so, we loop through all ShippingRuleGroups
         * get the first valid one, and process it for the price.
         */
        $paymentRule = $this->paymentRuleChecker->findValidPaymentRule($paymentProvider, $payable);

        if ($paymentRule instanceof PaymentRuleInterface) {
            $price = $this->paymentRuleProcessor->getPrice(
                $paymentRule,
                $paymentProvider,
                $payable,
                $context,
            );
            if (!$price) {
                $price = $payable->getPaymentTotal();
            }

            $modifications = $this->paymentRuleProcessor->getModification(
                $paymentRule,
                $paymentProvider,
                $payable,
                $price,
                $context,
            );

            return $modifications;
        }

        return 0;
    }
}
