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

pimcore.registerNS('coreshop.paymentproviderrule.conditions.zones');
coreshop.paymentproviderrule.conditions.zones = Class.create(coreshop.rules.conditions.abstract, {
    type: 'zones',

    getForm: function () {
        var me = this;

        var zones = {
            fieldLabel: t('coreshop_condition_zones'),
            typeAhead: true,
            listWidth: 100,
            width: 500,
            store: {
                type: 'coreshop_zones'
            },
            displayField: 'name',
            valueField: 'id',
            forceSelection: true,
            multiselect: true,
            triggerAction: 'all',
            name: 'zones',
            maxHeight: 400,
            delimiter: false,
            value: me.data.zones
        };

        if (this.data && this.data.zones) {
            zones.value = this.data.zones;
        }

        zones = new Ext.ux.form.MultiSelect(zones);

        this.form = new Ext.form.Panel({
            items: [
                zones
            ]
        });

        return this.form;
    }
});
