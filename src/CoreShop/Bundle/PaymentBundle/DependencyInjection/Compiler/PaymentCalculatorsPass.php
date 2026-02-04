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

namespace CoreShop\Bundle\PaymentBundle\DependencyInjection\Compiler;

use CoreShop\Component\Registry\RegisterSimpleRegistryTypePass;

final class PaymentCalculatorsPass extends RegisterSimpleRegistryTypePass
{
    public const string PAYMENT_PRICE_CALCULATOR_TAG = 'coreshop.payment.price_calculator';

    public function __construct(
        ) {
        parent::__construct(
            'coreshop.registry.payment.price_calculators',
            'coreshop.payment.price_calculators',
            self::PAYMENT_PRICE_CALCULATOR_TAG,
        );
    }
}
