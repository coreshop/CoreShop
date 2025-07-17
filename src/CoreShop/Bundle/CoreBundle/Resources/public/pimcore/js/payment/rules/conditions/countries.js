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

pimcore.registerNS('coreshop.paymentproviderrule.conditions.countries');
coreshop.paymentproviderrule.conditions.countries = Class.create(coreshop.rules.conditions.abstract, {
    type: 'countries',

    getForm: function () {
        var me = this;

        var countries = {
            fieldLabel: t('coreshop_condition_countries'),
            typeAhead: true,
            listWidth: 100,
            width: 500,
            store: {
                type: 'coreshop_countries'
            },
            displayField: 'name',
            valueField: 'id',
            forceSelection: true,
            multiselect: true,
            triggerAction: 'all',
            name: 'countries',
            height: 400,
            delimiter: false,
            value: me.data.countries
        };


        if (this.data && this.data.countries) {
            countries.value = this.data.countries;
        }

        countries = new Ext.ux.form.MultiSelect(countries);

        this.form = new Ext.form.Panel({
            items: [
                countries
            ]
        });

        return this.form;
    }
});
