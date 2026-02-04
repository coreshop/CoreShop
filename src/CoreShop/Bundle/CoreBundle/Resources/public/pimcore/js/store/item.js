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

coreshop.store.item = Class.create(coreshop.store.item, {

    getFormPanel: function ($super) {
        var me = this,
            panel = $super();

        panel.down('fieldset').add(
            [
                {
                    xtype: 'coreshop.country',
                    fieldLabel: t('coreshop_base_country'),
                    name: 'baseCountry',
                    value: this.data.baseCountry,
                    name: 'baseCountry',
                    store: {
                        proxy: {
                            type: 'ajax',
                            url: Routing.generate('coreshop_country_listActive'),
                            reader: {
                                type: 'json',
                            }
                        },
                        fields: [
                            {name: 'id'},
                            {name: 'name'}
                        ],
                        autoLoad: true,
                        remoteSort: false,
                        remoteFilter: false
                    }
                },
                {
                    xtype: 'checkbox',
                    fieldLabel: t('coreshop_use_gross_prices'),
                    value: this.data.useGrossPrice,
                    name: 'useGrossPrice'
                },
                {
                    xtype: 'multiselect',
                    fieldLabel: t('coreshop_allowed_countries'),
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
                }
            ]
        );

        return this.formPanel;
    }
});
