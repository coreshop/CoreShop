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

final class ShippingRuleActionPass extends RegisterRegistryTypePass
{
    public const SHIPPING_RULE_ACTION_TAG = 'coreshop.shipping_rule.action';

    public function __construct()
    {
        parent::__construct(
            'coreshop.registry.shipping_rule.actions',
            'coreshop.form_registry.shipping_rule.actions',
            'coreshop.shipping_rule.actions',
            self::SHIPPING_RULE_ACTION_TAG
        );
    }
}
