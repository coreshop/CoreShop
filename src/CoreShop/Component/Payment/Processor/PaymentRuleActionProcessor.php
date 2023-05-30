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

namespace CoreShop\Component\Payment\Processor;

use CoreShop\Component\Payment\Model\PayableInterface;
use CoreShop\Component\Payment\Model\PaymentProviderInterface;
use CoreShop\Component\Payment\Model\PaymentRuleInterface;
use CoreShop\Component\Payment\Rule\Action\ProviderPriceActionProcessorInterface;
use CoreShop\Component\Payment\Rule\Action\ProviderPriceModificationActionProcessorInterface;
use CoreShop\Component\Payment\Rule\Processor\PaymentRuleActionProcessorInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;

class PaymentRuleActionProcessor implements PaymentRuleActionProcessorInterface
{
    public function __construct(
        protected ServiceRegistryInterface $actionServiceRegistry,
    ) {
    }

    public function getPrice(
        PaymentRuleInterface $paymentRule,
        PaymentProviderInterface $paymentProvider,
        PayableInterface $payable,
        array $context,
    ): int {
        $price = 0;

        foreach ($paymentRule->getActions() as $action) {
            $processor = $this->actionServiceRegistry->get($action->getType());

            if ($processor instanceof ProviderPriceActionProcessorInterface) {
                dd($processor);
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

    public function getModification(
        PaymentRuleInterface $paymentRule,
        PaymentProviderInterface $paymentProvider,
        PayableInterface $payable,
        int $price,
        array $context,
    ): int {
        $modifications = 0;

        foreach ($paymentRule->getActions() as $action) {
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
