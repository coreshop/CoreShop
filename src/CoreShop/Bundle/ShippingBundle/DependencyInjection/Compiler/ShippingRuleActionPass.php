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

namespace CoreShop\Bundle\ShippingBundle\DependencyInjection\Compiler;

use CoreShop\Bundle\PimcoreBundle\DependencyInjection\Compiler\RegisterRegistryTypePass;

final class ShippingRuleActionPass extends RegisterRegistryTypePass
{
    public function __construct()
    {
        parent::__construct(
            'coreshop.registry.shipping_rule.actions',
            'coreshop.form_registry.shipping_rule.actions',
            'coreshop.shipping_rule.actions',
            'coreshop.shipping_rule.action'
        );
    }
}
