<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 */

declare(strict_types=1);

namespace CoreShop\Bundle\OrderBundle\DependencyInjection\Compiler;

use CoreShop\Component\Registry\RegisterRegistryTypePass;

final class CartItemPriceRuleActionPass extends RegisterRegistryTypePass
{
    public const CART_ITEM_PRICE_RULE_ACTION_TAG = 'coreshop.cart_item_price_rule.action';

    public function __construct()
    {
        parent::__construct(
            'coreshop.registry.cart_item_price_rule.actions',
            'coreshop.form_registry.cart_item_price_rule.actions',
            'coreshop.cart_item_price_rule.actions',
            self::CART_ITEM_PRICE_RULE_ACTION_TAG
        );
    }
}
