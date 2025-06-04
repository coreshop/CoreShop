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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\ShippingBundle\DependencyInjection\Compiler;

use CoreShop\Component\Registry\RegisterSimpleRegistryTypePass;

final class ShippingPriceCalculatorsPass extends RegisterSimpleRegistryTypePass
{
    public const SHIPPING_PRICE_CALCULATOR_TAG = 'coreshop.shipping.price_calculator';

    public function __construct(
        ) {
        parent::__construct(
            'coreshop.registry.shipping.price_calculators',
            'coreshop.shipping.price_calculators',
            self::SHIPPING_PRICE_CALCULATOR_TAG,
        );
    }
}
