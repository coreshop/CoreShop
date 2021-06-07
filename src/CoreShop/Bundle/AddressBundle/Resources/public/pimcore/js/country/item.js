/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.country.item');
coreshop.country.item = Class.create(coreshop.resource.item, {

    iconCls: 'coreshop_icon_country',

    routing: {
        save: 'coreshop_country_save'
    },

    getItems: function () {
        return [this.getFormPanel()];
    },

    getFormPanel: function () {
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
                    labelWidth: 350,
                    defaultType: 'textfield',
                    defaults: {width: 300},
                    items: items
                }
            ]
        });

        return this.formPanel;
    },

    getSaveData: function () {
        var values = this.formPanel.getForm().getFieldValues();

        if (!values['active']) {
            delete values['active'];
        }

        return values;
    }
});
