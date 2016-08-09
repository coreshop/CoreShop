/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (http://www.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.plugin.coreshop.settings');
pimcore.plugin.coreshop.settings = Class.create({

    shopPanels : {},

    initialize: function () {

        this.getData();
    },

    getData: function () {
        Ext.Ajax.request({
            url: '/plugin/CoreShop/admin_settings/get',
            success: function (response) {

                this.data = Ext.decode(response.responseText);

                this.getTabPanel();

            }.bind(this)
        });
    },

    getValue: function (shopId, key) {
        var current = null;

        if (this.data.values.hasOwnProperty(shopId)) {
            current = this.data.values[shopId];

            if(current.hasOwnProperty(key)) {
                current = current[key];
            }
        }

        if (typeof current != 'object' && typeof current != 'array' && typeof current != 'function') {
            return current;
        }

        return '';
    },

    getSystemValue : function(key) {
        var current = null;

        if (this.data.systemValues.hasOwnProperty(key)) {
            current = this.data.systemValues[key];
        }

        if (typeof current != 'object' && typeof current != 'array' && typeof current != 'function') {
            return current;
        }

        return '';
    },

    getClass : function(key, fromCurrentValues) {
        var lastValue = null;
        var firstLoop = true;

        if(fromCurrentValues == undefined) {
            fromCurrentValues = false;
        }

        for (var shopId in this.data.values) {
            if (!this.data.values.hasOwnProperty(shopId)) {
                return;
            }

            var value = this.getValue(shopId, key);

            if(fromCurrentValues) {
                value = this.shopPanels[shopId].down('[name="'+key+'"]').getValue();
            }

            if(firstLoop) {
                lastValue = value;
            }
            else {
                if(lastValue !== value) {
                    return '';
                }
            }

            firstLoop = false;
        }

        return this.getInheritanceClass();
    },

    getInheritanceClass : function() {
        return 'coreshop_settings_inherited';
    },

    getTabPanel: function () {
        if (!this.panel) {
            var me = this;

            this.panel = Ext.create('Ext.panel.Panel', {
                id: 'coreshop_settings',
                title: t('coreshop_settings'),
                iconCls: 'coreshop_icon_settings',
                border: false,
                layout: 'fit',
                closable:true
            });

            var tabPanel = Ext.getCmp('pimcore_panel_tabs');
            tabPanel.add(this.panel);
            tabPanel.setActiveItem('coreshop_settings');

            this.panel.on('destroy', function () {
                pimcore.globalmanager.remove('coreshop_settings');
            }.bind(this));

            this.exchangeRatesStore = new Ext.data.Store({
                proxy: {
                    type: 'ajax',
                    url : '/plugin/CoreShop/admin_currency/get-exchange-rate-providers',
                    reader: {
                        type: 'json',
                        rootProperty : 'data'
                    }
                }
            });
            this.exchangeRatesStore.load();

            this.messagingContactStore = pimcore.globalmanager.get('coreshop_messaging_contacts');
            this.messagingContactStore.load();

            this.layout = Ext.create('Ext.tab.Panel', {
                bodyStyle: 'padding:20px 5px 20px 5px;',
                border: false,
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
                ]
            });

            this.noMultishopPanel = Ext.create('Ext.form.Panel', {
                title: t('coreshop_system_settings'),
                border: false,
                autoScroll: true,
                forceLayout: true,
                defaults: {
                    forceLayout: true
                },
                items : [
                    {
                        xtype: 'fieldset',
                        title: t('coreshop_multishop'),
                        collapsible: true,
                        collapsed: true,
                        autoHeight: true,
                        labelWidth: 250,
                        defaultType: 'textfield',
                        defaults: {width: 600},
                        items: [
                            {
                                fieldLabel: t('coreshop_multishop_enabled'),
                                xtype: 'checkbox',
                                name: 'SYSTEM.MULTISHOP.ENABLED',
                                checked: this.getSystemValue('SYSTEM.MULTISHOP.ENABLED')
                            }
                        ]
                    },
                    {
                        xtype: 'fieldset',
                        title: t('coreshop_system_settings'),
                        collapsible: true,
                        collapsed: true,
                        autoHeight: true,
                        labelWidth: 250,
                        defaultType: 'textfield',
                        defaults: {width: 600},
                        items: [
                            {
                                fieldLabel: t('coreshop_send_usagelog'),
                                xtype: 'checkbox',
                                name: 'SYSTEM.LOG.USAGESTATISTICS',
                                checked: this.getSystemValue('SYSTEM.LOG.USAGESTATISTICS')
                            }
                        ]
                    },
                    {
                        xtype: 'fieldset',
                        title: t('coreshop_currency'),
                        collapsible: true,
                        collapsed: true,
                        autoHeight: true,
                        labelWidth: 250,
                        defaultType: 'textfield',
                        defaults: {width: 600},
                        items: [
                            {
                                fieldLabel: t('coreshop_currency_automatic_exchange_rates'),
                                xtype: 'checkbox',
                                name: 'SYSTEM.CURRENCY.AUTO_EXCHANGE_RATES',
                                checked: this.getValue(shopId, 'SYSTEM.CURRENCY.AUTO_EXCHANGE_RATES'),
                                listeners: {
                                    change: function (checkbox, newValue) {
                                        checkbox.up('fieldset').down('[name="SYSTEM.CURRENCY.EXCHANGE_RATE_PROVIDER"]').setHidden(!newValue);
                                        checkbox.up('fieldset').down('[name="SYSTEM.CURRENCY.EXCHANGE_RATE_PROVIDER"]').setDisabled(!newValue);

                                        checkbox.up('fieldset').down('label').setHidden(!newValue);
                                    }
                                }
                            },
                            {
                                xtype: 'combo',
                                fieldLabel: t('coreshop_currency_exchange_rate_provider'),
                                name: 'SYSTEM.CURRENCY.EXCHANGE_RATE_PROVIDER',
                                value: this.getValue(shopId, 'SYSTEM.CURRENCY.EXCHANGE_RATE_PROVIDER'),
                                width: 500,
                                hidden: !this.getValue(shopId, 'SYSTEM.CURRENCY.AUTO_EXCHANGE_RATES'),
                                disabled: !this.getValue(shopId, 'SYSTEM.CURRENCY.AUTO_EXCHANGE_RATES'),
                                store: this.exchangeRatesStore,
                                triggerAction: 'all',
                                typeAhead: false,
                                editable: false,
                                forceSelection: true,
                                queryMode: 'local',
                                displayField: 'name',
                                valueField: 'name'
                            },
                            {
                                xtype: 'label',
                                text: t('coreshop_currency_exchange_rate_last_update') + ': ' + Ext.Date.format(new Date(this.getValue(shopId, 'SYSTEM.CURRENCY.LAST_EXCHANGE_UPDATE') * 1000), Ext.Date.defaultFormat),
                                hidden: !this.getValue(shopId, 'SYSTEM.CURRENCY.AUTO_EXCHANGE_RATES'),
                            }
                        ]
                    },
                ]
            });

            if(coreshop.settings.multishop) {
                for (var shopId in this.data.values) {
                    if (!this.data.values.hasOwnProperty(shopId)) {
                        return;
                    }

                    this.shopPanels[shopId] = this.getConfigFormForShop(shopId);
                    this.layout.add(this.shopPanels[shopId]);
                }
            }
            else {
                var shopPanel = this.getConfigFormForShop(coreshop.settings.defaultShop);

                this.shopPanels[coreshop.settings.defaultShop] = shopPanel;

                this.layout.add(shopPanel);
            }


            this.layout.add(this.noMultishopPanel);

            this.panel.add(this.layout);
            this.layout.setActiveItem(0);

            pimcore.layout.refresh();
        }

        return this.panel;
    },

    activate: function () {
        var tabPanel = Ext.getCmp('pimcore_panel_tabs');
        tabPanel.setActiveItem('coreshop_settings');
    },

    save: function () {
        var values = {};

        for(var shopId in this.shopPanels) {
            if(this.shopPanels.hasOwnProperty(shopId)) {
                values[shopId] = this.shopPanels[shopId].getForm().getFieldValues();
            }
        }

        var data = this.noMultishopPanel.getForm().getFieldValues();

        Ext.Ajax.request({
            url: '/plugin/CoreShop/admin_settings/set',
            method: 'post',
            params: {
                values: Ext.encode(values),
                systemValues : Ext.encode(data)
            },
            success: function (response) {
                try {
                    var res = Ext.decode(response.responseText);
                    if (res.success) {
                        pimcore.helpers.showNotification(t('success'), t('coreshop_settings_save_success'), 'success');

                        Ext.MessageBox.confirm(t("info"), t("reload_pimcore_changes"), function (buttonValue) {
                            if (buttonValue == "yes") {
                                window.location.reload();
                            }
                        }.bind(this));

                    } else {
                        pimcore.helpers.showNotification(t('error'), t('coreshop_settings_save_error'),
                            'error', t(res.message));
                    }
                } catch (e) {
                    pimcore.helpers.showNotification(t('error'), t('coreshop_settings_save_error'), 'error');
                }
            }
        });
    },
    
    elementChanged : function(el, newValue, oldValue, eOpts) {
        var elements = this.panel.query('[name="'+el.getName()+'"]');

        if(elements) {
            Ext.each(elements, function (element) {
                element.removeCls(this.getInheritanceClass());
                element.addCls(this.getClass(el.getName(), true));
            }.bind(this));
        }
    },

    checkForInheritance : function(element) {
        var me = this;

        if(coreshop.settings.multishop) {
            if (element['items']) {
                Ext.each(element.items.items, function (item) {
                    if (item['getName']) {
                        item.addCls(me.getClass(item.getName()));
                        item.removeListener('change', me.elementChanged.bind(me));
                        item.addListener('change', me.elementChanged.bind(me));
                    }

                    if (item['items']) {
                        me.checkForInheritance(item);
                    }
                });
            }
        }
    },

    getConfigFormForShop : function(shopId) {
        var me = this;
        var messagingLangTabs = [];
        var store = pimcore.globalmanager.get('coreshop_shops');
        var shop = store.getById(shopId);
        if (!shop) {
            alert("SHOP NOT FOUND!");
            return;
        }

        Ext.each(pimcore.settings.websiteLanguages, function (lang) {
            var shortLang = lang.toUpperCase();

            if (shortLang.indexOf('-') > 0) {
                shortLang = shortLang.split('-');
                shortLang = shortLang[0];
            }

            var tab = {
                title: pimcore.available_languages[lang],
                iconCls: 'pimcore_icon_language_' + lang.toLowerCase(),
                layout: 'form',
                items: [
                    {
                        name: 'SYSTEM.MESSAGING.MAIL.CUSTOMER.' + shortLang,
                        value: me.getValue(shopId, 'SYSTEM.MESSAGING.MAIL.CUSTOMER.' + shortLang),
                        fieldLabel: t('coreshop_messaging_customer_email'),
                        labelWidth: 350,
                        fieldCls: 'pimcore_droptarget_input',
                        xtype: 'textfield',
                        listeners: {
                            render: function (el) {
                                new Ext.dd.DropZone(el.getEl(), {
                                    reference: this,
                                    ddGroup: 'element',
                                    getTargetFromEvent: function (e) {
                                        return this.getEl();
                                    }.bind(el),

                                    onNodeOver: function (target, dd, e, data) {
                                        data = data.records[0].data;

                                        if (data.elementType == 'document') {
                                            return Ext.dd.DropZone.prototype.dropAllowed;
                                        }

                                        return Ext.dd.DropZone.prototype.dropNotAllowed;
                                    },

                                    onNodeDrop: function (target, dd, e, data) {
                                        data = data.records[0].data;

                                        if (data.elementType == 'document') {
                                            this.setValue(data.id);
                                            return true;
                                        }

                                        return false;
                                    }.bind(el)
                                });
                            }
                        }
                    },
                    {
                        name: 'SYSTEM.MESSAGING.MAIL.CUSTOMER.RE.' + shortLang,
                        value: me.getValue(shopId, 'SYSTEM.MESSAGING.MAIL.CUSTOMER.RE.' + shortLang),
                        fieldLabel: t('coreshop_messaging_customer_re_email'),
                        labelWidth: 350,
                        fieldCls: 'pimcore_droptarget_input',
                        xtype: 'textfield',
                        listeners: {
                            render: function (el) {
                                new Ext.dd.DropZone(el.getEl(), {
                                    reference: this,
                                    ddGroup: 'element',
                                    getTargetFromEvent: function (e) {
                                        return this.getEl();
                                    }.bind(el),

                                    onNodeOver: function (target, dd, e, data) {
                                        data = data.records[0].data;

                                        if (data.elementType == 'document') {
                                            return Ext.dd.DropZone.prototype.dropAllowed;
                                        }

                                        return Ext.dd.DropZone.prototype.dropNotAllowed;
                                    },

                                    onNodeDrop: function (target, dd, e, data) {
                                        data = data.records[0].data;

                                        if (data.elementType == 'document') {
                                            this.setValue(data.id);
                                            return true;
                                        }

                                        return false;
                                    }.bind(el)
                                });
                            }
                        }
                    },
                    {
                        name: 'SYSTEM.MESSAGING.MAIL.CONTACT.' + shortLang,
                        value: me.getValue(shopId, 'SYSTEM.MESSAGING.MAIL.CONTACT.' + shortLang),
                        fieldLabel: t('coreshop_messaging_contact_email'),
                        labelWidth: 350,
                        fieldCls: 'pimcore_droptarget_input',
                        xtype: 'textfield',
                        listeners: {
                            render: function (el) {
                                new Ext.dd.DropZone(el.getEl(), {
                                    reference: this,
                                    ddGroup: 'element',
                                    getTargetFromEvent: function (e) {
                                        return this.getEl();
                                    }.bind(el),

                                    onNodeOver: function (target, dd, e, data) {
                                        data = data.records[0].data;

                                        if (data.elementType == 'document') {
                                            return Ext.dd.DropZone.prototype.dropAllowed;
                                        }

                                        return Ext.dd.DropZone.prototype.dropNotAllowed;
                                    },

                                    onNodeDrop: function (target, dd, e, data) {
                                        data = data.records[0].data;

                                        if (data.elementType == 'document') {
                                            this.setValue(data.id);
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

            messagingLangTabs.push(tab);
        });

        var shopPanel = Ext.create('Ext.form.Panel', {
            title : shop.get("name"),
            border: false,
            autoScroll: true,
            forceLayout: true,
            defaults: {
                forceLayout: true,
                listeners : {
                    render : function(el) {
                        me.checkForInheritance(el);
                    }
                }
            },
            fieldDefaults: {
                labelWidth: 250
            },
            items: [
                {
                    xtype: 'fieldset',
                    title: t('coreshop_debug'),
                    collapsible: true,
                    collapsed: true,
                    autoHeight: true,
                    labelWidth: 250,
                    defaultType: 'textfield',
                    defaults: {width: 600},
                    items: [
                        {
                            fieldLabel: t('coreshop_show_debug'),
                            xtype: 'checkbox',
                            name: 'SYSTEM.BASE.SHOWDEBUG',
                            checked: this.getValue(shopId, 'SYSTEM.BASE.SHOWDEBUG')
                        }
                    ]
                },
                {
                    xtype: 'fieldset',
                    title: t('coreshop_base'),
                    collapsible: true,
                    collapsed: true,
                    autoHeight: true,
                    labelWidth: 250,
                    defaultType: 'textfield',
                    defaults: {width: 600},
                    items: [
                        {
                            xtype: 'combo',
                            fieldLabel: t('coreshop_base_country'),
                            typeAhead: true,
                            value: this.getValue(shopId, 'SYSTEM.BASE.COUNTRY'),
                            mode: 'local',
                            listWidth: 100,
                            store: pimcore.globalmanager.get('coreshop_countries'),
                            displayField: 'name',
                            valueField: 'id',
                            forceSelection: true,
                            triggerAction: 'all',
                            name: 'SYSTEM.BASE.COUNTRY',
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
                            fieldLabel: t('coreshop_base_catalogmode'),
                            xtype: 'checkbox',
                            name: 'SYSTEM.BASE.CATALOGMODE',
                            checked: this.getValue(shopId, 'SYSTEM.BASE.CATALOGMODE')
                        },
                        {
                            fieldLabel: t('coreshop_base_guestcheckout'),
                            xtype: 'checkbox',
                            name: 'SYSTEM.BASE.GUESTCHECKOUT',
                            checked: this.getValue(shopId, 'SYSTEM.BASE.GUESTCHECKOUT')
                        }
                    ]
                },
                {
                    xtype: 'fieldset',
                    title: t('coreshop_messaging'),
                    collapsible: true,
                    collapsed: true,
                    autoHeight: true,
                    labelWidth: 250,
                    defaultType: 'textfield',
                    defaults: {width: 600},
                    items: [
                        {
                            xtype: 'tabpanel',
                            activeTab: 0,
                            width: '100%',
                            defaults: {
                                autoHeight: true,
                                bodyStyle: 'padding:10px;'
                            },
                            items: messagingLangTabs

                        },
                        {
                            xtype: 'combo',
                            fieldLabel: t('coreshop_messaging_contact_sales'),
                            name: 'SYSTEM.MESSAGING.CONTACT.SALES',
                            value: this.getValue(shopId, 'SYSTEM.MESSAGING.CONTACT.SALES'),
                            width: 500,
                            store: this.messagingContactStore,
                            triggerAction: 'all',
                            typeAhead: false,
                            editable: false,
                            forceSelection: true,
                            queryMode: 'local',
                            displayField: 'text',
                            valueField: 'id'
                        },
                        {
                            xtype: 'combo',
                            fieldLabel: t('coreshop_messaging_contact_technology'),
                            name: 'SYSTEM.MESSAGING.CONTACT.TECHNOLOGY',
                            value: this.getValue(shopId, 'SYSTEM.MESSAGING.CONTACT.TECHNOLOGY'),
                            width: 500,
                            store: this.messagingContactStore,
                            triggerAction: 'all',
                            typeAhead: false,
                            editable: false,
                            forceSelection: true,
                            queryMode: 'local',
                            displayField: 'text',
                            valueField: 'id'
                        }
                    ]
                },
                {
                    xtype: 'fieldset',
                    title: t('coreshop_stock'),
                    collapsible: true,
                    collapsed: true,
                    autoHeight: true,
                    labelWidth: 250,
                    defaultType: 'textfield',
                    defaults: {width: 600},
                    items: [
                        {
                            fieldLabel: t('coreshop_stock_defaultoutofstock_behavior'),
                            name: 'SYSTEM.STOCK.DEFAULTOUTOFSTOCKBEHAVIOUR',
                            value: this.getValue(shopId, 'SYSTEM.STOCK.DEFAULTOUTOFSTOCKBEHAVIOUR'),
                            width: 500,
                            xtype: 'combo',
                            store: [[0, t('coreshop_stock_deny_order')], [1, t('coreshop_stock_allow_order')]],
                            triggerAction: 'all',
                            typeAhead: false,
                            editable: false,
                            forceSelection: true,
                            queryMode: 'local'
                        }
                    ]
                },
                {
                    xtype: 'fieldset',
                    title: t('coreshop_tax'),
                    collapsible: true,
                    collapsed: true,
                    autoHeight: true,
                    labelWidth: 250,
                    defaultType: 'textfield',
                    defaults: {width: 600},
                    items: [
                        {
                            fieldLabel: t('coreshop_base_tax_enabled'),
                            xtype: 'checkbox',
                            name: 'SYSTEM.BASE.TAX.ENABLED',
                            checked: this.getValue(shopId, 'SYSTEM.BASE.TAX.ENABLED')
                        },
                        {
                            fieldLabel: t('coreshop_base_checkvat'),
                            xtype: 'checkbox',
                            name: 'SYSTEM.BASE.CHECKVAT',
                            checked: this.getValue(shopId, 'SYSTEM.BASE.CHECKVAT')
                        },
                        {
                            fieldLabel: t('coreshop_base_disablevatforbasecountry'),
                            xtype: 'checkbox',
                            name: 'SYSTEM.BASE.DISABLEVATFORBASECOUNTRY',
                            checked: this.getValue(shopId, 'SYSTEM.BASE.DISABLEVATFORBASECOUNTRY')
                        },
                        {
                            fieldLabel: t('coreshop_taxation_address'),
                            name: 'SYSTEM.BASE.TAXATION.ADDRESS',
                            value: this.getValue(shopId, 'SYSTEM.BASE.TAXATION.ADDRESS') ? this.getValue(shopId, 'SYSTEM.BASE.TAXATION.ADDRESS') : 'shipping',
                            width: 500,
                            xtype: 'combo',
                            store: [['shipping', t('coreshop_taxation_address_shipping')], ['billing', t('coreshop_taxation_address_billing')]],
                            triggerAction: 'all',
                            typeAhead: false,
                            editable: false,
                            forceSelection: true,
                            queryMode: 'local'
                        }
                    ]
                },
                {
                    xtype: 'fieldset',
                    title: t('coreshop_currency'),
                    collapsible: true,
                    collapsed: true,
                    autoHeight: true,
                    labelWidth: 250,
                    defaultType: 'textfield',
                    defaults: {width: 600},
                    items: [
                        {
                            xtype: 'combo',
                            fieldLabel: t('coreshop_base_currency'),
                            typeAhead: true,
                            value: this.getValue(shopId, 'SYSTEM.BASE.CURRENCY'),
                            mode: 'local',
                            listWidth: 100,
                            store: pimcore.globalmanager.get('coreshop_currencies'),
                            displayField: 'name',
                            valueField: 'id',
                            forceSelection: true,
                            triggerAction: 'all',
                            name: 'SYSTEM.BASE.CURRENCY',
                            listeners: {
                                change: function () {
                                    this.forceReloadOnSave = true;
                                }.bind(this),
                                select: function () {
                                    this.forceReloadOnSave = true;
                                }.bind(this)
                            }
                        }
                    ]
                },
                {
                    xtype: 'fieldset',
                    title: t('coreshop_prices'),
                    collapsible: true,
                    collapsed: true,
                    autoHeight: true,
                    labelWidth: 250,
                    defaultType: 'textfield',
                    defaults: {width: 600},
                    items: [
                        {
                            fieldLabel: t('coreshop_prices_are_gross'),
                            xtype: 'checkbox',
                            name: 'SYSTEM.BASE.PRICES.GROSS',
                            checked: this.getValue(shopId, 'SYSTEM.BASE.PRICES.GROSS')
                        }
                    ]
                },
                {
                    xtype: 'fieldset',
                    title: t('coreshop_shipping'),
                    collapsible: true,
                    collapsed: true,
                    autoHeight: true,
                    labelWidth: 250,
                    defaultType: 'textfield',
                    defaults: {width: 600},
                    items: [
                        {
                            xtype: 'numberfield',
                            name: 'SYSTEM.SHIPPING.FREESHIPPING_WEIGHT',
                            fieldLabel: t('coreshop_freeshipping_weight'),
                            width: 500,
                            value: this.getValue(shopId, 'SYSTEM.SHIPPING.FREESHIPPING_WEIGHT'),
                            decimalPrecision: 2,
                            step: 1
                        },
                        {
                            xtype: 'numberfield',
                            name: 'SYSTEM.SHIPPING.FREESHIPPING_PRICE',
                            fieldLabel: t('coreshop_freeshipping_price'),
                            width: 500,
                            value: this.getValue(shopId, 'SYSTEM.SHIPPING.FREESHIPPING_PRICE'),
                            decimalPrecision: 2,
                            step: 1
                        },
                        {
                            fieldLabel: t('coreshop_carrier_sort'),
                            name: 'SYSTEM.SHIPPING.CARRIER_SORT',
                            value: this.getValue(shopId, 'SYSTEM.SHIPPING.CARRIER_SORT'),
                            width: 500,
                            xtype: 'combo',
                            store: [['price', t('coreshop_carrier_sort_price')], ['grade', t('coreshop_carrier_sort_grade')]],
                            triggerAction: 'all',
                            typeAhead: false,
                            editable: false,
                            forceSelection: true,
                            queryMode: 'local'
                        }
                    ]
                },
                {
                    xtype: 'fieldset',
                    title: t('coreshop_product'),
                    collapsible: true,
                    collapsed: true,
                    autoHeight: true,
                    labelWidth: 250,
                    defaultType: 'textfield',
                    defaults: {width: 600},
                    items: [
                        {
                            fieldLabel: t('coreshop_default_image'),
                            name: 'SYSTEM.PRODUCT.DEFAULTIMAGE',
                            cls: 'input_drop_target',
                            value: this.getValue(shopId, 'SYSTEM.PRODUCT.DEFAULTIMAGE'),
                            xtype: 'textfield',
                            listeners: {
                                render: function (el) {
                                    new Ext.dd.DropZone(el.getEl(), {
                                        reference: this,
                                        ddGroup: 'element',
                                        getTargetFromEvent: function (e) {
                                            return this.getEl();
                                        }.bind(el),

                                        onNodeOver: function (target, dd, e, data) {
                                            data = data.records[0].data;

                                            if (data.elementType == 'asset') {
                                                return Ext.dd.DropZone.prototype.dropAllowed;
                                            }

                                            return Ext.dd.DropZone.prototype.dropNotAllowed;
                                        },

                                        onNodeDrop: function (target, dd, e, data) {
                                            data = data.records[0].data;

                                            if (data.elementType == 'asset') {
                                                this.setValue(data.path);
                                                return true;
                                            }

                                            return false;
                                        }.bind(el)
                                    });
                                }
                            }
                        },
                        {
                            fieldLabel: t('coreshop_product_daysasnew'),
                            name: 'SYSTEM.PRODUCT.DAYSASNEW',
                            value: this.getValue(shopId, 'SYSTEM.PRODUCT.DAYSASNEW'),
                            xtype: 'spinnerfield',
                            enableKeyEvents: true
                        }
                    ]
                },
                {
                    xtype: 'fieldset',
                    title: t('coreshop_category'),
                    collapsible: true,
                    collapsed: true,
                    autoHeight: true,
                    labelWidth: 250,
                    defaultType: 'textfield',
                    defaults: {width: 600},
                    items: [
                        {
                            fieldLabel: t('coreshop_default_image'),
                            name: 'SYSTEM.CATEGORY.DEFAULTIMAGE',
                            cls: 'input_drop_target',
                            value: this.getValue(shopId, 'SYSTEM.CATEGORY.DEFAULTIMAGE'),
                            xtype: 'textfield',
                            listeners: {
                                render: function (el) {
                                    new Ext.dd.DropZone(el.getEl(), {
                                        reference: this,
                                        ddGroup: 'element',
                                        getTargetFromEvent: function (e) {
                                            return this.getEl();
                                        }.bind(el),

                                        onNodeOver: function (target, dd, e, data) {
                                            data = data.records[0].data;

                                            if (data.elementType == 'asset') {
                                                return Ext.dd.DropZone.prototype.dropAllowed;
                                            }

                                            return Ext.dd.DropZone.prototype.dropNotAllowed;
                                        },

                                        onNodeDrop: function (target, dd, e, data) {
                                            data = data.records[0].data;

                                            if (data.elementType == 'asset') {
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
                },
                {
                    xtype: 'fieldset',
                    title: t('coreshop_invoice'),
                    collapsible: true,
                    collapsed: true,
                    autoHeight: true,
                    labelWidth: 250,
                    defaultType: 'textfield',
                    defaults: {width: 600},
                    items: [
                        {
                            fieldLabel: t('coreshop_invoice_create'),
                            xtype: 'checkbox',
                            name: 'SYSTEM.INVOICE.CREATE',
                            checked: this.getValue(shopId, 'SYSTEM.INVOICE.CREATE')
                        },
                        {
                            fieldLabel: t('coreshop_invoice_prefix'),
                            name: 'SYSTEM.INVOICE.PREFIX',
                            value: this.getValue(shopId, 'SYSTEM.INVOICE.PREFIX')
                        },
                        {
                            fieldLabel: t('coreshop_invoice_suffix'),
                            name: 'SYSTEM.INVOICE.SUFFIX',
                            value: this.getValue(shopId, 'SYSTEM.INVOICE.SUFFIX')
                        },
                        {
                            fieldLabel: t('coreshop_invoice_wkhtmltopdf_params'),
                            name: 'SYSTEM.INVOICE.WKHTML',
                            value: this.getValue(shopId, 'SYSTEM.INVOICE.WKHTML')
                        }
                    ]
                },
                {
                    xtype: 'fieldset',
                    title: t('coreshop_mail'),
                    collapsible: true,
                    collapsed: true,
                    autoHeight: true,
                    labelWidth: 250,
                    defaultType: 'textfield',
                    defaults: {width: 600},
                    items: [
                        {
                            fieldLabel: t('coreshop_mail_order_notification'),
                            name: 'SYSTEM.MAIL.ORDER.NOTIFICATION',
                            regex: /^([\w+-.%]+@[\w-.]+\.[A-Za-z]{2,4},?)+$/i,
                            emptyText: 'john@doe.com,jane@doe.com',
                            value: this.getValue(shopId, 'SYSTEM.MAIL.ORDER.NOTIFICATION')
                        },
                        {
                            fieldLabel: t('coreshop_mail_order_bbc'),
                            name: 'SYSTEM.MAIL.ORDER.BCC',
                            xtype: 'checkbox',
                            checked: this.getValue(shopId, 'SYSTEM.MAIL.ORDER.BCC')

                        }
                    ]
                },
                {
                    xtype: 'fieldset',
                    title: t('coreshop_cart'),
                    collapsible: true,
                    collapsed: true,
                    autoHeight: true,
                    labelWidth: 250,
                    defaultType: 'textfield',
                    defaults: {width: 600},
                    items: [
                        {
                            fieldLabel: t('coreshop_cart_activate_auto_cleanup'),
                            name: 'SYSTEM.CART.AUTO_CLEANUP',
                            xtype: 'checkbox',
                            checked: this.getValue(shopId, 'SYSTEM.CART.AUTO_CLEANUP'),
                            listeners: {
                                change: function (checkbox, checked) {
                                    if (checked) {
                                        Ext.getCmp('coreshop_cart_activate_auto_cleanup_settings_' + shopId).show();
                                    } else {
                                        Ext.getCmp('coreshop_cart_activate_auto_cleanup_settings_' + shopId).hide();
                                    }
                                }
                            }
                        },
                        {
                            xtype: 'fieldset',
                            title: t('coreshop_cart_activate_auto_cleanup_settings'),
                            id: 'coreshop_cart_activate_auto_cleanup_settings_' + shopId,
                            collapsible: false,
                            collapsed: false,
                            autoHeight: true,
                            hidden: !this.getValue(shopId, 'SYSTEM.CART.AUTO_CLEANUP'),
                            labelWidth: 250,
                            defaultType: 'textfield',
                            items: [
                                {
                                    fieldLabel: t('coreshop_cart_cleanup_older_than_days'),
                                    name: 'SYSTEM.CART.AUTO_CLEANUP.OLDER_THAN_DAYS',
                                    xtype: 'numberfield',
                                    minValue: 0,
                                    value: this.getValue(shopId, 'SYSTEM.CART.AUTO_CLEANUP.OLDER_THAN_DAYS')

                                },
                                {
                                    fieldLabel: t('coreshop_cart_cleanup_delete_anonymous_carts'),
                                    name: 'SYSTEM.CART.AUTO_CLEANUP.DELETE_ANONYMOUS',
                                    xtype: 'checkbox',
                                    checked: this.getValue(shopId, 'SYSTEM.CART.AUTO_CLEANUP.DELETE_ANONYMOUS')

                                },
                                {
                                    fieldLabel: t('coreshop_cart_cleanup_delete_user_carts'),
                                    name: 'SYSTEM.CART.AUTO_CLEANUP.DELETE_USER',
                                    xtype: 'checkbox',
                                    checked: this.getValue(shopId, 'SYSTEM.CART.AUTO_CLEANUP.DELETE_USER')

                                }
                            ]
                        }

                    ]
                }
            ]
        });

        return shopPanel;
    }
});
