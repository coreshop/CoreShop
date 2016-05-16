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

pimcore.registerNS('pimcore.plugin.coreshop.orderstates.item');

pimcore.plugin.coreshop.orderstates.item = Class.create(pimcore.plugin.coreshop.abstract.item, {

    iconCls : 'coreshop_icon_order_states',

    url : {
        save : '/plugin/CoreShop/admin_order-state/save'
    },

    getItems : function () {
        return [this.getFormPanel()];
    },

    getTitleText : function () {
        return this.data.localizedFields.items[pimcore.settings.language].name;
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
                items: [
                    {
                        xtype: 'textfield',
                        name: 'name.' + lang,
                        fieldLabel: t('name'),
                        width: 400,
                        value: data.localizedFields.items[lang] ? data.localizedFields.items[lang].name : ''
                    },
                    {
                        fieldLabel: t('coreshop_order_state_emailDocument'),
                        labelWidth: 350,
                        name: 'emailDocument.' + lang,
                        fieldCls: 'pimcore_droptarget_input',
                        value: data.localizedFields.items[lang] ? data.localizedFields.items[lang].emailDocument : '',
                        xtype: 'textfield',
                        listeners: {
                            render: function (el) {
                                new Ext.dd.DropZone(el.getEl(), {
                                    reference: this,
                                    ddGroup: 'element',
                                    getTargetFromEvent: function (e) {
                                        return this.getEl();
                                    }.bind(el),

                                    onNodeOver : function (target, dd, e, data) {
                                        data = data.records[0].data;

                                        if (data.elementType == 'document') {
                                            return Ext.dd.DropZone.prototype.dropAllowed;
                                        }

                                        return Ext.dd.DropZone.prototype.dropNotAllowed;
                                    },

                                    onNodeDrop : function (target, dd, e, data) {
                                        data = data.records[0].data;

                                        if (data.elementType == 'document') {
                                            this.setValue(data.path);
                                            return true;
                                        }

                                        return false;
                                    }.bind(el)
                                });
                            }
                        }
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
                    defaults: { width: '100%' },
                    items :[
                        {
                            xtype: 'checkbox',
                            name: 'accepted',
                            fieldLabel: t('coreshop_order_state_accepted'),
                            width: 250,
                            checked: parseInt(data.accepted)
                        }, {
                            xtype: 'checkbox',
                            name: 'shipped',
                            fieldLabel: t('coreshop_order_state_shipped'),
                            width: 250,
                            checked: parseInt(data.shipped)
                        }, {
                            xtype: 'checkbox',
                            name: 'paid',
                            fieldLabel: t('coreshop_order_state_paid'),
                            width: 250,
                            checked: parseInt(data.paid)
                        }, {
                            xtype: 'checkbox',
                            name: 'invoice',
                            fieldLabel: t('coreshop_order_state_invoice'),
                            width: 250,
                            checked: parseInt(data.invoice)
                        }, {
                            xtype: 'checkbox',
                            name: 'email',
                            fieldLabel: t('coreshop_order_state_email'),
                            width: 250,
                            checked: parseInt(data.email)
                        }, {
                            xtype: 'textfield',
                            name : 'color',
                            fieldLabel: t('coreshop_order_state_color'),
                            width : 250,
                            value : data.color ? data.color : '#000000',
                            style : {
                                backgroundColor : data.color
                            },
                            listeners : {
                                change : function (txtfield, newValue) {
                                    if (/(^#[0-9A-F]{6}$)|(^#[0-9A-F]{3}$)/.test(newValue)) {
                                        txtfield.setStyle('background-color', newValue);
                                    }
                                }
                            }
                        }, {
                            xtype: 'tabpanel',
                            activeTab: 0,
                            defaults: {
                                autoHeight:true,
                                bodyStyle:'padding:10px;'
                            },
                            items: langTabs
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
