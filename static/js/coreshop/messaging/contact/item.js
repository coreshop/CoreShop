/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.plugin.coreshop.messaging.contact.item');
pimcore.plugin.coreshop.messaging.contact.item = Class.create(pimcore.plugin.coreshop.abstract.item, {

    iconCls : 'coreshop_icon_messaging_contact',

    url : {
        save : '/plugin/CoreShop/admin_Messaging-Contact/save'
    },

    getItems : function () {
        return [this.getFormPanel()];
    },

    getTitleText : function () {
        return this.data.localizedFields.items[pimcore.settings.language].name;
    },

    getFormPanel : function () {
        var langTabs = [],
            data = this.data;

        Ext.each(pimcore.settings.websiteLanguages, function (lang) {
            var tab = {
                title: pimcore.available_languages[lang],
                iconCls: 'pimcore_icon_language_' + lang.toLowerCase(),
                layout:'form',
                items: [
                    {
                        xtype: 'textfield',
                        name: 'name.' + lang,
                        fieldLabel: t('name'),
                        labelWidth: 350,
                        value: data.localizedFields.items[lang] ? data.localizedFields.items[lang].name : ''
                    }
                ]
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
                    defaults: { width: 300 },
                    items :[
                        {
                            xtype: 'tabpanel',
                            activeTab: 0,
                            width : '100%',
                            defaults: {
                                autoHeight:true,
                                bodyStyle:'padding:10px;'
                            },
                            items: langTabs
                        },
                        {
                            fieldLabel: t('email'),
                            name: 'email',
                            value: this.data.email
                        },
                        {
                            fieldLabel: t('description'),
                            name: 'description',
                            width : '100%',
                            checked: this.data.description
                        }
                    ]
                }
            ]
        });

        return this.formPanel;
    },

    getSaveData : function () {
        return {
            data: Ext.encode(this.formPanel.getForm().getFieldValues())
        };
    }
});
