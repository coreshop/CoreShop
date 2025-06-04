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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\OrderBundle\DependencyInjection\Compiler;

use CoreShop\Component\Registry\RegisterRegistryTypePass;

final class CartItemPriceRuleConditionPass extends RegisterRegistryTypePass
{
    public const CART_ITEM_PRICE_RULE_CONDITION_TAG = 'coreshop.cart_item_price_rule.condition';

    public function __construct(
        ) {
        parent::__construct(
            'coreshop.registry.cart_item_price_rule.conditions',
            'coreshop.form_registry.cart_item_price_rule.conditions',
            'coreshop.cart_item_price_rule.conditions',
            self::CART_ITEM_PRICE_RULE_CONDITION_TAG,
        );
    }
}
