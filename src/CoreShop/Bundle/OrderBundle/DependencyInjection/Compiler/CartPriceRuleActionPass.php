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

final class CartPriceRuleActionPass extends RegisterRegistryTypePass
{
    public const string CART_PRICE_RULE_ACTION_TAG = 'coreshop.cart_price_rule.action';

    public function __construct(
        ) {
        parent::__construct(
            'coreshop.registry.cart_price_rule.actions',
            'coreshop.form_registry.cart_price_rule.actions',
            'coreshop.cart_price_rule.actions',
            self::CART_PRICE_RULE_ACTION_TAG,
        );
    }
}
