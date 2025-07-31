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

pimcore.registerNS('coreshop.country.item');
coreshop.country.item = Class.create(coreshop.resource.item, {

    iconCls: 'coreshop_icon_country',

    routing: {
        save: 'coreshop_country_save'
    },

    getFormPanelItems: function () {
        var data = this.data,
            langTabs = [],
            salutationsStore = Ext.create('Ext.data.ArrayStore', {
                fields: ['name']
            });

        Ext.each(pimcore.settings.websiteLanguages, function (lang) {
            var tab = {
                title: pimcore.available_languages[lang],
                iconCls: 'pimcore_icon_language_' + lang.toLowerCase(),
                layout: 'form',
                items: [{
                    xtype: 'textfield',
                    name: 'translations.' + lang + '.name',
                    fieldLabel: t('name'),
                    width: 400,
                    value: data.translations[lang] ? data.translations[lang].name : ''
                }]
            };

            langTabs.push(tab);
        });

        var items = [
            {
                xtype: 'tabpanel',
                activeTab: 0,
                defaults: {
                    autoHeight: true,
                    bodyStyle: 'padding:10px;'
                },
                width: '100%',
                items: langTabs
            },
            {
                fieldLabel: t('coreshop_country_isoCode'),
                name: 'isoCode',
                value: data.isoCode
            },
            {
                xtype: 'checkbox',
                fieldLabel: t('active'),
                name: 'active',
                inputValue: true,
                uncheckedValue: false,
                value: data.active
            },
            {
                xtype: 'coreshop.zone',
                value: data.zone
            },
            {
                fieldLabel: t('coreshop_country_addressFormat'),
                xtype: 'textarea',
                name: 'addressFormat',
                value: data.addressFormat,
                width: 500,
                height: 400
            },
            {
                xtype: 'tagfield',
                fieldLabel: t('coreshop_country_salutations'),
                store: new Ext.data.ArrayStore({
                    fields: [
                        'salutation'
                    ],
                    data: []
                }),
                value: data.salutations,
                name: 'salutations',
                createNewOnEnter: true,
                createNewOnBlur: true,
                queryMode: 'local',
                displayField: 'salutation',
                valueField: 'salutation',
                hideTrigger: true
            }
        ];

        return items;
    },

    getSaveData: function () {
        var values = this.formPanel.getForm().getFieldValues();

        if (!values['active']) {
            delete values['active'];
        }

        return values;
    }
});
