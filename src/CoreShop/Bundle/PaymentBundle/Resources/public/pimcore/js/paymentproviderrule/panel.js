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

pimcore.registerNS('coreshop.paymentproviderrule.panel');
coreshop.paymentproviderrule.panel = Class.create(coreshop.rules.panel, {
    /**
     * @var string
     */
    layoutId: 'coreshop_payment_provider_rule_panel',
    storeId: 'coreshop_payment_provider_rules',
    iconCls: 'coreshop_nav_icon_payment_provider_rule',
    type: 'coreshop_payment_provider_rule',

    routing: {
        add: 'coreshop_payment_provider_rule_add',
        delete: 'coreshop_payment_provider_rule_delete',
        get: 'coreshop_payment_provider_rule_get',
        list: 'coreshop_payment_provider_rule_list',
        config: 'coreshop_payment_provider_rule_getConfig'
    },

    getItemClass: function () {
        return coreshop.paymentproviderrule.item;
    }
});
