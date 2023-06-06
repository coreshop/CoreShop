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

namespace CoreShop\Component\Payment\Rule\Action;

use CoreShop\Component\Payment\Model\PayableInterface;
use CoreShop\Component\Payment\Model\PaymentProviderInterface;
use CoreShop\Component\Payment\Model\PaymentProviderRuleInterface;
use CoreShop\Component\Payment\Rule\Processor\PaymentProviderRuleActionProcessorInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;

class PaymentProviderRuleActionProcessor implements ProviderPriceActionProcessorInterface, ProviderPriceModificationActionProcessorInterface
{
    public function __construct(
        protected PaymentProviderRuleActionProcessorInterface $paymentProviderRuleProcessor,
        protected RepositoryInterface $paymentProviderRuleRepository,
    ) {
    }

    public function getPrice(
        PaymentProviderInterface $paymentProvider,
        PayableInterface $payable,
        array $configuration,
        array $context,
    ): int {
        $paymentProviderRule = $this->paymentProviderRuleRepository->find($configuration['paymentProviderRule']);

        if ($paymentProviderRule instanceof PaymentProviderRuleInterface) {
            return $this->paymentProviderRuleProcessor->getPrice($paymentProviderRule, $paymentProvider, $payable, $context);
        }

        return 0;
    }

    public function getModification(
        PaymentProviderInterface $paymentProvider,
        PayableInterface $payable,
        int $price,
        array $configuration,
        array $context,
    ): int {
        $paymentProviderRule = $this->paymentProviderRuleRepository->find($configuration['paymentProviderRule']);

        if ($paymentProviderRule instanceof PaymentProviderRuleInterface) {
            return $this->paymentProviderRuleProcessor->getModification($paymentProviderRule, $paymentProvider, $payable, $price, $context);
        }

        return 0;
    }
}
