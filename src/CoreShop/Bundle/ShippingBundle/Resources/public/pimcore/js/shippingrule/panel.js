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

pimcore.registerNS('coreshop.shippingrule.panel');
coreshop.shippingrule.panel = Class.create(coreshop.rules.panel, {
    /**
     * @var string
     */
    layoutId: 'coreshop_carrier_shipping_rule_panel',
    storeId: 'coreshop_carrier_shipping_rules',
    iconCls: 'coreshop_icon_carrier_shipping_rule',
    type: 'coreshop_carriers_shipping_rules',
    permission: 'coreshop_permission_shipping_rule',

    routing: {
        add: 'coreshop_shipping_rule_add',
        delete: 'coreshop_shipping_rule_delete',
        get: 'coreshop_shipping_rule_get',
        list: 'coreshop_shipping_rule_list',
        config: 'coreshop_shipping_rule_getConfig'
    },

    getItemClass: function () {
        return coreshop.shippingrule.item;
    }
});
