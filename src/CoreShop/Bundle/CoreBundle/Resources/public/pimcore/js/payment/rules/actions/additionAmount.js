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

pimcore.registerNS('coreshop.paymentproviderrule.actions.additionAmount');
coreshop.paymentproviderrule.actions.additionAmount = Class.create(coreshop.rules.actions.abstract, {
    type: 'discountAmount',

    getForm: function () {
        var amountValue = 0;
        var currency = null;

        if (this.data) {
            amountValue = this.data.amount / pimcore.globalmanager.get('coreshop.currency.decimal_factor');
            currency = this.data.currency;
        }

        var amount = new Ext.form.NumberField({
            fieldLabel: t('coreshop_action_discountAmount_amount'),
            name: 'amount',
            value: amountValue,
            decimalPrecision: pimcore.globalmanager.get('coreshop.currency.decimal_precision')
        });

        this.form = new Ext.form.Panel({
            items: [
                amount,
                {
                    xtype: 'coreshop.currency',
                    value: currency
                }
            ]
        });

        return this.form;
    }
});
