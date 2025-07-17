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

use CoreShop\Component\Registry\RegisterRegistryTypePass;

final class ShippingRuleConditionPass extends RegisterRegistryTypePass
{
    public const string SHIPPING_RULE_CONDITION_TAG = 'coreshop.shipping_rule.condition';

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
