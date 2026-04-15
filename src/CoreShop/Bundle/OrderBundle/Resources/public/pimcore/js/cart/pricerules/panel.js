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

pimcore.registerNS('coreshop.cart.pricerules.panel');

coreshop.cart.pricerules.panel = Class.create(coreshop.rules.panel, {

    /**
     * @var string
     */
    layoutId: 'coreshop_price_rules_panel',
    storeId: 'coreshop_cart_price_rules',
    iconCls: 'coreshop_icon_price_rule',
    type: 'coreshop_cart_pricerules',

    routing: {
        add: 'coreshop_cart_price_rule_add',
        delete: 'coreshop_cart_price_rule_delete',
        get: 'coreshop_cart_price_rule_get',
        list: 'coreshop_cart_price_rule_list',
        config: 'coreshop_cart_price_rule_getConfig'
    },

    getItemClass: function () {
        return coreshop.cart.pricerules.item;
    }
});
