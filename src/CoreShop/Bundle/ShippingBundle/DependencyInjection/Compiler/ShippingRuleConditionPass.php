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

declare(strict_types=1);

namespace CoreShop\Bundle\ShippingBundle\DependencyInjection\Compiler;

use CoreShop\Component\Registry\RegisterRegistryTypePass;

final class ShippingRuleConditionPass extends RegisterRegistryTypePass
{
    public const SHIPPING_RULE_CONDITION_TAG = 'coreshop.shipping_rule.condition';

    public function __construct()
    {
        parent::__construct(
            'coreshop.registry.shipping_rule.conditions',
            'coreshop.form_registry.shipping_rule.conditions',
            'coreshop.shipping_rule.conditions',
            self::SHIPPING_RULE_CONDITION_TAG
        );
    }
}
