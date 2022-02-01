<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\ShippingBundle\DependencyInjection\Compiler;

use CoreShop\Component\Registry\RegisterRegistryTypePass;

final class ShippingTaxCalculationStrategyPass extends RegisterRegistryTypePass
{
    public const SHIPPING_TAX_STRATEGY_TAG = 'coreshop.shipping.tax_calculation_strategy';

    public function __construct()
    {
        parent::__construct(
            'coreshop.registry.shipping.tax_calculation_strategies',
            'coreshop.form_registry.shipping.tax_calculation_strategies',
            'coreshop.shipping.tax_calculation_strategies',
            self::SHIPPING_TAX_STRATEGY_TAG
        );
    }
}
