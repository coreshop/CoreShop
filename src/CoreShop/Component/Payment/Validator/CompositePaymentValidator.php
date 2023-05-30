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

namespace CoreShop\Component\Payment\Validator;

use Laminas\Stdlib\PriorityQueue;

class CompositePaymentValidator implements PaymentProviderInterface
{
    private PriorityQueue $paymentProviderValidator;

    public function __construct(
        ) {
        $this->paymentProviderValidator = new PriorityQueue();
    }

    public function addValidator(PaymentProviderInterface $paymentProviderValidator, int $priority = 0): void
    {
        $this->paymentProviderValidator->insert($paymentProviderValidator, $priority);
    }

    public function isPaymentValid(): bool
    {
        return true;
    }
}
