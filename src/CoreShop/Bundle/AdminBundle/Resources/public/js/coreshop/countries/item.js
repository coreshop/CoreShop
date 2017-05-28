/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('pimcore.plugin.coreshop.countries.item');
pimcore.plugin.coreshop.countries.item = Class.create(pimcore.plugin.coreshop.abstract.item, {

    iconCls: 'coreshop_icon_country',

    url: {
        save: '/admin/CoreShop/countries/save'
    },

    getItems: function () {
        return [this.getFormPanel()];
    },

    getFormPanel: function () {
        var data = this.data,
            langTabs = [];

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
            },
            {
                xtype: 'combo',
                fieldLabel: t('coreshop_country_currency'),
                typeAhead: true,
                value: data.currency,
                mode: 'local',
                listWidth: 100,
                store: pimcore.globalmanager.get('coreshop_currencies'),
                displayField: 'name',
                valueField: 'id',
                forceSelection: true,
                triggerAction: 'all',
                name: 'currency',
                listeners: {
                    change: function () {
                        this.forceReloadOnSave = true;
                    }.bind(this),
                    select: function () {
                        this.forceReloadOnSave = true;
                    }.bind(this)
                }
            },
            {
                xtype: 'combo',
                fieldLabel: t('coreshop_country_zone'),
                typeAhead: true,
                value: data.zone,
                mode: 'local',
                listWidth: 100,
                store: pimcore.globalmanager.get('coreshop_zones'),
                displayField: 'name',
                valueField: 'id',
                forceSelection: true,
                triggerAction: 'all',
                name: 'zone',
                listeners: {
                    change: function () {
                        this.forceReloadOnSave = true;
                    }.bind(this),
                    select: function () {
                        this.forceReloadOnSave = true;
                    }.bind(this)
                }
            },
            {
                fieldLabel: t('coreshop_country_addressFormat'),
                xtype: 'textarea',
                name: 'addressFormat',
                value: data.addressFormat
            }
        ];

        items.push(this.getMultishopSettings());

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
