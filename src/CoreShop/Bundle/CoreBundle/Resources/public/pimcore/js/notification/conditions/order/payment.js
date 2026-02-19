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

pimcore.registerNS('coreshop.notification.rule.conditions.payment');

coreshop.notification.rule.conditions.payment = Class.create(coreshop.rules.conditions.abstract, {
    type: 'payment',

    getForm: function () {
        var paymentProvidersStore = new Ext.data.Store({
            proxy: {
                type: 'ajax',
                url: Routing.generate('coreshop_payment_provider_list'),
                reader: {
                    type: 'json',
                    rootProperty: 'data'
                }
            },
            fields: ['id', 'identifier']
        });
        paymentProvidersStore.load();

        var providers = new Ext.ux.form.MultiSelect({
            typeAhead: true,
            listWidth: 100,
            width: 500,
            forceSelection: true,
            maxHeight: 400,
            delimiter: false,
            labelWidth: 150,
            fieldLabel: t('coreshop_paymentProvider'),
            mode: 'local',
            store: paymentProvidersStore,
            displayField: 'identifier',
            valueField: 'id',
            triggerAction: 'all',
            name: 'providers',
            multiSelect: true,
            value: this.data ? this.data.providers : []
        });

        this.form = Ext.create('Ext.form.FieldSet', {
            items: [
                providers
            ]
        });

        return this.form;
    }
});
