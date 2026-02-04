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

pimcore.registerNS('coreshop.product.pricerule.conditions.not_combinable_with_cart_price_voucher_rule');
coreshop.product.pricerule.conditions.not_combinable_with_cart_price_voucher_rule = Class.create(coreshop.rules.conditions.abstract, {
    type: 'not_combinable_with_cart_price_voucher_rule',

    getForm: function () {
        var me = this;

        var price_rules = {
            fieldLabel: t('coreshop_condition_not_combinable'),
            typeAhead: true,
            listWidth: 100,
            width: 500,
            store: {
                type: 'coreshop_cart_price_voucher_rules'
            },
            displayField: 'name',
            valueField: 'id',
            forceSelection: true,
            multiselect: true,
            triggerAction: 'all',
            name: 'price_rules',
            height: 400,
            delimiter: false,
            value: me.data.countries
        };


        if (this.data && this.data.price_rules) {
            price_rules.value = this.data.price_rules;
        }

        price_rules = new Ext.ux.form.MultiSelect(price_rules);

        this.form = new Ext.form.Panel({
            items: [
                price_rules
            ]
        });

        return this.form;
    }
});