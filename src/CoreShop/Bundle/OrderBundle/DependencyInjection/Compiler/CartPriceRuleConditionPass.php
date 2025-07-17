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

namespace CoreShop\Bundle\OrderBundle\DependencyInjection\Compiler;

use CoreShop\Component\Registry\RegisterRegistryTypePass;

final class CartPriceRuleConditionPass extends RegisterRegistryTypePass
{
    public const string CART_PRICE_RULE_CONDITION_TAG = 'coreshop.cart_price_rule.condition';

    public function __construct(
        ) {
        parent::__construct(
            'coreshop.registry.cart_price_rule.conditions',
            'coreshop.form_registry.cart_price_rule.conditions',
            'coreshop.cart_price_rule.conditions',
            self::CART_PRICE_RULE_CONDITION_TAG,
        );
    }
}
