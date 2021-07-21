/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.tax.item');
coreshop.tax.item = Class.create(coreshop.resource.item, {

    iconCls: 'coreshop_icon_taxes',

    routing: {
        save: 'coreshop_tax_rate_save'
    },

    getItems: function () {
        return [this.getFormPanel()];
    },

    getTitleText: function () {
        return this.data.name;
    },

    getFormPanel: function () {
        var data = this.data;

        var langTabs = [];
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
                    defaults: {width: '100%'},
                    items: [
                        {
                            xtype: 'tabpanel',
                            activeTab: 0,
                            defaults: {
                                autoHeight: true,
                                bodyStyle: 'padding:10px;'
                            },
                            items: langTabs
                        },
                        {
                            xtype: 'numberfield',
                            name: 'rate',
                            fieldLabel: t('coreshop_tax_rate'),
                            width: 400,
                            value: data.rate,
                            decimalPrecision: 2,
                            step: 1
                        }, {
                            xtype: 'checkbox',
                            name: 'active',
                            fieldLabel: t('active'),
                            width: 250,
                            checked: data.active
                        }
                    ]
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
