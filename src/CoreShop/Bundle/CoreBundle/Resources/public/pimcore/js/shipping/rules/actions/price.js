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

pimcore.registerNS('coreshop.shippingrule.actions.price');
coreshop.shippingrule.actions.price = Class.create(coreshop.rules.actions.abstract, {
    type: 'price',

    getForm: function () {
        var priceValue = 0;
        var currency = null;

        if (this.data) {
            priceValue = this.data.price / pimcore.globalmanager.get('coreshop.currency.decimal_factor');
            currency = this.data.currency;
        }

        var price = new Ext.form.NumberField({
            fieldLabel: t('coreshop_action_price'),
            name: 'price',
            value: priceValue,
            decimalPrecision: pimcore.globalmanager.get('coreshop.currency.decimal_precision')
        });

        this.form = new Ext.form.Panel({
            items: [
                price,
                {
                    xtype: 'coreshop.currency',
                    value: currency
                }
            ]
        });

        return this.form;
    }
});
