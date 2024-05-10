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

class AdditionPercentActionProcessor implements ProviderPriceModificationActionProcessorInterface
{
    public function getModification(PaymentProviderInterface $paymentProvider, PayableInterface $payable, int $price, array $configuration, array $context): int
    {
        return (int) round($price * ($configuration['percent'] / 100));
    }
}
