<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\OrderBundle\DependencyInjection\Compiler;

use CoreShop\Component\Registry\RegisterRegistryTypePass;

final class CartPriceRuleConditionPass extends RegisterRegistryTypePass
{
    public const CART_PRICE_RULE_CONDITION_TAG = 'coreshop.cart_price_rule.condition';

    public function __construct()
    {
        parent::__construct(
            'coreshop.registry.cart_price_rule.conditions',
            'coreshop.form_registry.cart_price_rule.conditions',
            'coreshop.cart_price_rule.conditions',
            self::CART_PRICE_RULE_CONDITION_TAG
        );
    }
}
