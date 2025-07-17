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

final class ShippingRuleActionPass extends RegisterRegistryTypePass
{
    public const string SHIPPING_RULE_ACTION_TAG = 'coreshop.shipping_rule.action';

    public function __construct(
        ) {
        parent::__construct(
            'coreshop.registry.shipping_rule.actions',
            'coreshop.form_registry.shipping_rule.actions',
            'coreshop.shipping_rule.actions',
            self::SHIPPING_RULE_ACTION_TAG,
        );
    }
}
