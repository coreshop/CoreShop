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

pimcore.registerNS('coreshop.currency.item');
coreshop.currency.item = Class.create(coreshop.resource.item, {

    iconCls: 'coreshop_icon_currency',

    routing: {
        save: 'coreshop_currency_save'
    },

    getItems: function () {
        return [this.getFormPanel()];
    },

    getFormPanel: function () {
        this.formPanel = new Ext.form.Panel({
            bodyStyle: 'padding:20px 5px 20px 5px;',
            border: false,
            region: 'center',
            autoScroll: true,
            forceLayout: true,
            defaults: {
                forceLayout: true
            },
            buttons: [
                {
                    text: t('save'),
                    handler: this.save.bind(this),
                    iconCls: 'pimcore_icon_apply'
                }
            ],
            items: [
                {
                    xtype: 'fieldset',
                    autoHeight: true,
                    labelWidth: 250,
                    defaultType: 'textfield',
                    defaults: {width: 300},
                    items: [
                        {
                            fieldLabel: t('name'),
                            name: 'name',
                            value: this.data.name
                        },
                        {
                            fieldLabel: t('coreshop_currency_isoCode'),
                            name: 'isoCode',
                            value: this.data.isoCode
                        },
                        {
                            xtype: 'numberfield',
                            fieldLabel: t('coreshop_currency_numericIsoCode'),
                            name: 'numericIsoCode',
                            value: this.data.numericIsoCode
                        },
                        {
                            fieldLabel: t('coreshop_currency_symbol'),
                            name: 'symbol',
                            value: this.data.symbol
                        }
                    ]
                }
            ]
        });

        return this.formPanel;
    },

    getSaveData: function () {
        return this.formPanel.getForm().getFieldValues();
    }
});
