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

pimcore.registerNS('coreshop.cart.pricerules.conditions.voucher');
coreshop.cart.pricerules.conditions.voucher = Class.create(coreshop.rules.conditions.abstract, {
    type: 'voucher',

    getForm: function () {

        this.form = new Ext.form.Panel({
            items: [{
                fieldLabel: t('coreshop_action_voucher_max_usage_per_code'),
                xtype: 'numberfield',
                name: 'maxUsagePerCode',
                value: this.data.maxUsagePerCode
            },{
                fieldLabel: t('coreshop_action_voucher_max_usage_per_user'),
                xtype: 'numberfield',
                name: 'maxUsagePerUser',
                value: this.data.maxUsagePerUser
            },
            {
                fieldLabel: t('coreshop_action_voucher_only_one_per_cart'),
                xtype: 'checkbox',
                name: 'onlyOnePerCart',
                value: this.data.onlyOnePerCart
            }]
        });

        return this.form;
    },

    getValues: function () {
        return this.form.getForm().getValues();
    }
});
