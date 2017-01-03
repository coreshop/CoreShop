/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.plugin.coreshop.taxes.item');

pimcore.plugin.coreshop.taxes.item = Class.create(pimcore.plugin.coreshop.abstract.item, {

    iconCls : 'coreshop_icon_taxes',

    url : {
        save : '/plugin/CoreShop/admin_tax/save'
    },

    getItems : function () {
        return [this.getFormPanel()];
    },

    getTitleText : function () {
        return this.data.title;
    },

    getFormPanel : function ()
    {
        var data = this.data;

        var langTabs = [];
        Ext.each(pimcore.settings.websiteLanguages, function (lang) {
            var tab = {
                title: pimcore.available_languages[lang],
                iconCls: 'pimcore_icon_language_' + lang.toLowerCase(),
                layout:'form',
                items: [{
                    xtype: 'textfield',
                    name: 'name.' + lang,
                    fieldLabel: t('name'),
                    width: 400,
                    value: data.localizedFields.items[lang] ? data.localizedFields.items[lang].name : ''
                }]
            };

            langTabs.push(tab);
        });

        this.formPanel = new Ext.form.Panel({
            bodyStyle:'padding:20px 5px 20px 5px;',
            border: false,
            region : 'center',
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
                    xtype:'fieldset',
                    autoHeight:true,
                    labelWidth: 350,
                    defaultType: 'textfield',
                    defaults: { width: '100%' },
                    items :[
                        {
                            xtype: 'tabpanel',
                            activeTab: 0,
                            defaults: {
                                autoHeight:true,
                                bodyStyle:'padding:10px;'
                            },
                            items: langTabs
                        },
                        {
                            xtype: 'numberfield',
                            name: 'rate',
                            fieldLabel: t('coreshop_tax_rate'),
                            width: 400,
                            value: data.rate,
                            decimalPrecision : 2,
                            step : 1
                        }, {
                            xtype: 'checkbox',
                            name: 'active',
                            fieldLabel: t('coreshop_tax_active'),
                            width: 250,
                            checked: data.active
                        }
                    ]
                }
            ]
        });

        return this.formPanel;
    },

    getSaveData : function () {
        return {
            data : Ext.encode(this.formPanel.getForm().getFieldValues())
        };
    },

    save: function ()
    {
        var values = this.formPanel.getForm().getFieldValues();

        Ext.Ajax.request({
            url: '/plugin/CoreShop/admin_tax/save',
            method: 'post',
            params: {
                data: Ext.encode(values),
                id : this.data.id
            },
            success: function (response) {
                try {
                    var res = Ext.decode(response.responseText);
                    if (res.success) {
                        pimcore.helpers.showNotification(t('success'), t('coreshop_tax_saved_successfully'), 'success');
                    } else {
                        pimcore.helpers.showNotification(t('error'), t('coreshop_tax_saved_error'),
                            'error', t(res.message));
                    }
                } catch (e) {
                    pimcore.helpers.showNotification(t('error'), t('coreshop_tax_saved_error'), 'error');
                }
            }
        });
    }
});
