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

namespace CoreShop\Bundle\ShippingBundle\DependencyInjection\Compiler;

use CoreShop\Component\Registry\PrioritizedCompositeServicePass;
use CoreShop\Component\Shipping\Validator\CompositeShippableCarrierValidator;
use CoreShop\Component\Shipping\Validator\ShippableCarrierValidatorInterface;

final class CompositeShippableValidatorPass extends PrioritizedCompositeServicePass
{
    public const string SHIPABLE_VALIDATOR_TAG = 'coreshop.shipping.carrier.validator';

    public function __construct(
        ) {
        parent::__construct(
            ShippableCarrierValidatorInterface::class,
            CompositeShippableCarrierValidator::class,
            self::SHIPABLE_VALIDATOR_TAG,
            'addValidator',
        );
    }
}
