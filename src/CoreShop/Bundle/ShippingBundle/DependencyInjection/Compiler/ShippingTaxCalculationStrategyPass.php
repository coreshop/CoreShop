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

use CoreShop\Component\Registry\RegisterRegistryTypePass;

final class ShippingTaxCalculationStrategyPass extends RegisterRegistryTypePass
{
    public const SHIPPING_TAX_STRATEGY_TAG = 'coreshop.shipping.tax_calculation_strategy';

    public function __construct(
        ) {
        parent::__construct(
            'coreshop.registry.shipping.tax_calculation_strategies',
            'coreshop.form_registry.shipping.tax_calculation_strategies',
            'coreshop.shipping.tax_calculation_strategies',
            self::SHIPPING_TAX_STRATEGY_TAG,
        );
    }
}
