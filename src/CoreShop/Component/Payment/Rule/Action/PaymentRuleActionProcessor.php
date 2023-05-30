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
use CoreShop\Component\Payment\Model\PaymentRuleInterface;
use CoreShop\Component\Payment\Rule\Processor\PaymentRuleActionProcessorInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;

class PaymentRuleActionProcessor implements ProviderPriceActionProcessorInterface, ProviderPriceModificationActionProcessorInterface
{
    public function __construct(
        protected PaymentRuleActionProcessorInterface $paymentRuleProcessor,
        protected RepositoryInterface $paymentRuleRepository,
    ) {
    }

    public function getPrice(
        PaymentProviderInterface $paymentProvider,
        PayableInterface $payable,
        array $configuration,
        array $context,
    ): int {
        $paymentRule = $this->paymentRuleRepository->find($configuration['paymentRule']);

        if ($paymentRule instanceof PaymentRuleInterface) {
            return $this->paymentRuleProcessor->getPrice($paymentRule, $paymentProvider, $payable, $context);
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
        $paymentRule = $this->paymentRuleRepository->find($configuration['paymentRule']);

        if ($paymentRule instanceof PaymentRuleInterface) {
            return $this->paymentRuleProcessor->getModification($paymentRule, $paymentProvider,$configuration, $price, $context);
        }

        return 0;
    }
}
