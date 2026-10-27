<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Component\Payment\Processor;

use CoreShop\Component\Payment\Model\PayableInterface;
use CoreShop\Component\Payment\Model\PaymentProviderInterface;
use CoreShop\Component\Payment\Model\PaymentProviderRuleInterface;
use CoreShop\Component\Payment\Rule\Action\ProviderPriceActionProcessorInterface;
use CoreShop\Component\Payment\Rule\Action\ProviderPriceModificationActionProcessorInterface;
use CoreShop\Component\Payment\Rule\Processor\PaymentProviderRuleActionProcessorInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;
use CoreShop\Component\Rule\Recursion\RuleRecursionGuard;
use CoreShop\Component\Rule\Recursion\RuleRecursionGuardInterface;

class PaymentProviderRuleActionProcessor implements PaymentProviderRuleActionProcessorInterface
{
    public function __construct(
        protected ServiceRegistryInterface $actionServiceRegistry,
        protected RuleRecursionGuardInterface $recursionGuard = new RuleRecursionGuard(),
    ) {
    }

    public function getPrice(
        PaymentProviderRuleInterface $paymentProviderRule,
        PaymentProviderInterface $paymentProvider,
        PayableInterface $payable,
        array $context,
    ): int {
        // A rule that ends up calculating its own price again (e.g. through a "paymentProviderRule" action) adds nothing
        return $this->recursionGuard->guard(
            RuleRecursionGuardInterface::SCOPE_ACTIONS_PRICE,
            $paymentProviderRule,
            $paymentProvider,
            fn (): int => $this->calculatePrice($paymentProviderRule, $paymentProvider, $payable, $context),
            0,
        );
    }

    public function getModification(
        PaymentProviderRuleInterface $paymentProviderRule,
        PaymentProviderInterface $paymentProvider,
        PayableInterface $payable,
        int $price,
        array $context,
    ): int {
        return $this->recursionGuard->guard(
            RuleRecursionGuardInterface::SCOPE_ACTIONS_MODIFICATION,
            $paymentProviderRule,
            $paymentProvider,
            fn (): int => $this->calculateModification($paymentProviderRule, $paymentProvider, $payable, $price, $context),
            0,
        );
    }

    protected function calculatePrice(
        PaymentProviderRuleInterface $paymentProviderRule,
        PaymentProviderInterface $paymentProvider,
        PayableInterface $payable,
        array $context,
    ): int {
        $price = 0;

        foreach ($paymentProviderRule->getActions() as $action) {
            $processor = $this->actionServiceRegistry->get($action->getType());

            if ($processor instanceof ProviderPriceActionProcessorInterface) {
                $price += $processor->getPrice(
                    $paymentProvider,
                    $payable,
                    $action->getConfiguration(),
                    $context,
                );
            }
        }

        return $price;
    }

    protected function calculateModification(
        PaymentProviderRuleInterface $paymentProviderRule,
        PaymentProviderInterface $paymentProvider,
        PayableInterface $payable,
        int $price,
        array $context,
    ): int {
        $modifications = 0;

        foreach ($paymentProviderRule->getActions() as $action) {
            $processor = $this->actionServiceRegistry->get($action->getType());

            if ($processor instanceof ProviderPriceModificationActionProcessorInterface) {
                $modifications += $processor->getModification(
                    $paymentProvider,
                    $payable,
                    $price,
                    $action->getConfiguration(),
                    $context,
                );
            }
        }

        return $modifications;
    }
}
