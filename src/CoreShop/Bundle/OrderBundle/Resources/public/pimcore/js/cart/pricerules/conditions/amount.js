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

pimcore.registerNS('coreshop.cart.pricerules.conditions.amount');
coreshop.cart.pricerules.conditions.amount = Class.create(coreshop.rules.conditions.abstract, {

    type: 'amount',

    getForm: function () {
        var minAmountValue = 0;
        var maxAmountValue = 0;
        var me = this;

        if (this.data && this.data.minAmount) {
            minAmountValue = this.data.minAmount / pimcore.globalmanager.get('coreshop.currency.decimal_factor');
        }

        if (this.data && this.data.maxAmount) {
            maxAmountValue = this.data.maxAmount / pimcore.globalmanager.get('coreshop.currency.decimal_factor');
        }

        var minAmount = new Ext.form.NumberField({
            fieldLabel: t('coreshop_condition_amount_minAmount'),
            name: 'minAmount',
            value: minAmountValue,
            minValue: 0,
            decimalPrecision: pimcore.globalmanager.get('coreshop.currency.decimal_precision')
        });

        var maxAmount = new Ext.form.NumberField({
            fieldLabel: t('coreshop_condition_amount_maxAmount'),
            name: 'maxAmount',
            value: maxAmountValue,
            minValue: 0,
            decimalPrecision: pimcore.globalmanager.get('coreshop.currency.decimal_precision')
        });

        this.form = Ext.create('Ext.form.Panel', {
            items: [
                minAmount, maxAmount
            ]
        });

        return this.form;
    }
});
