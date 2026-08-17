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

pimcore.registerNS('coreshop.shippingrule.conditions.currencies');
coreshop.shippingrule.conditions.currencies = Class.create(coreshop.rules.conditions.abstract, {
    type: 'currencies',

    getForm: function () {
        var me = this;

        var currencies = {
            fieldLabel: t('coreshop_condition_currencies'),
            typeAhead: true,
            listWidth: 100,
            width: 500,
            store: {
                type: 'coreshop_currencies'
            },
            displayField: 'name',
            valueField: 'id',
            forceSelection: true,
            multiselect: true,
            triggerAction: 'all',
            name: 'currencies',
            maxHeight: 400,
            delimiter: false,
            value: me.data.currencies
        };

        if (this.data && this.data.currencies) {
            currencies.value = this.data.currencies;
        }

        currencies = new Ext.ux.form.MultiSelect(currencies);

        this.form = new Ext.form.Panel({
            items: [
                currencies
            ]
        });

        return this.form;
    }
});
