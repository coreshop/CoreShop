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

namespace CoreShop\Component\Core\Model;

use CoreShop\Component\PayumPayment\Model\PaymentProvider as BasePaymentProvider;
use CoreShop\Component\Store\Model\StoresAwareTrait;

/**
 * @psalm-suppress MissingConstructor
 */
class PaymentProvider extends BasePaymentProvider implements PaymentProviderInterface
{
    use StoresAwareTrait {
        __construct as storesAwareConstructor;
    }

    public function __construct(
        ) {
        parent::__construct();

        $this->storesAwareConstructor();
    }
}
