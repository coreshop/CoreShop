<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\ProductBundle\DependencyInjection\Compiler;

use CoreShop\Bundle\PimcoreBundle\DependencyInjection\Compiler\RegisterRegistryTypePass;

final class ProductPriceRuleConditionPass extends RegisterRegistryTypePass
{
    public function __construct()
    {
        parent::__construct(
            'coreshop.registry.product_price_rule.conditions',
            'coreshop.form_registry.product_price_rule.conditions',
            'coreshop.product_price_rule.conditions',
            'coreshop.product_price_rule.condition'
        );
    }
}
