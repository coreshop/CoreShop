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

namespace CoreShop\Bundle\ShippingBundle\DependencyInjection\Compiler;

use CoreShop\Component\Registry\RegisterRegistryTypePass;

final class ShippingRuleConditionPass extends RegisterRegistryTypePass
{
    public const SHIPPING_RULE_CONDITION_TAG = 'coreshop.shipping_rule.condition';

    public function __construct(
        ) {
        parent::__construct(
            'coreshop.registry.shipping_rule.conditions',
            'coreshop.form_registry.shipping_rule.conditions',
            'coreshop.shipping_rule.conditions',
            self::SHIPPING_RULE_CONDITION_TAG,
        );
    }
}
