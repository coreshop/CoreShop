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

pimcore.registerNS('coreshop.settings');
coreshop.settings = Class.create({

    shopPanels: {},

    initialize: function () {

        this.getData();
    },

    getData: function () {
        Ext.Ajax.request({
            url: '/admin/coreshop/configurations/get-all',
            success: function (response) {

                this.data = Ext.decode(response.responseText).data;

                this.getTabPanel();

            }.bind(this)
        });
    },

    getValue: function (shopId, key) {
        var current = null;

        if (this.data.hasOwnProperty(shopId)) {
            var shopValues = this.data[shopId];

            if (shopValues.hasOwnProperty(key)) {
                current = shopValues[key];
            }
        }

        if (current !== null && typeof current !== 'function') {
            return current;
        }

        return '';
    },

    getClass: function (key, fromCurrentValues) {
        var lastValue = null;
        var firstLoop = true;

        if (fromCurrentValues === undefined) {
            fromCurrentValues = false;
        }

        for (var shopId in this.data) {
            if (!this.data.hasOwnProperty(shopId)) {
                return;
            }

            var value = this.getValue(shopId, key);

            if (fromCurrentValues) {
                value = this.shopPanels[shopId].down('[name="' + key + '"]').getValue();
            }

            if (firstLoop) {
                lastValue = value;
            } else {
                if (Ext.isArray(value) && Ext.isArray(lastValue)) {
                    var diff = Ext.Array.difference(lastValue, value);

                    if (Ext.isArray(diff) && diff.length > 0) {
                        return '';
                    }
                }
                else if (lastValue !== value) {
                    return '';
                }
            }

            firstLoop = false;
        }

        return this.getInheritanceClass();
    },

    getInheritanceClass: function () {
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
                closable: true
            });

            var tabPanel = Ext.getCmp('pimcore_panel_tabs');
            tabPanel.add(this.panel);
            tabPanel.setActiveItem('coreshop_settings');

            this.panel.on('destroy', function () {
                pimcore.globalmanager.remove('coreshop_settings');
            }.bind(this));

            /*this.exchangeRatesStore = new Ext.data.Store({
             proxy: {
             type: 'ajax',
             url : '/admin/coreshop/currency/get-exchange-rate-providers',
             reader: {
             type: 'json',
             rootProperty : 'data'
             }
             }
             });

             this.exchangeRatesStore.load();*/

            /*this.messagingContactStore = pimcore.globalmanager.get('coreshop_messaging_contacts');
             this.messagingContactStore.load();*/

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

            for (var shopId in this.data) {
                if (!this.data.hasOwnProperty(shopId)) {
                    return;
                }

                this.shopPanels[shopId] = this.getConfigFormForShop(shopId);
                this.layout.add(this.shopPanels[shopId]);
            }

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

        for (var shopId in this.shopPanels) {
            if (this.shopPanels.hasOwnProperty(shopId)) {
                values[shopId] = this.shopPanels[shopId].getForm().getFieldValues();
            }
        }

        Ext.Ajax.request({
            url: '/admin/coreshop/configurations/save-all',
            method: 'post',
            params: {
                values: Ext.encode(values),
            },
            success: function (response) {
                try {
                    var res = Ext.decode(response.responseText);
                    if (res.success) {
                        pimcore.helpers.showNotification(t('success'), t('coreshop_settings_save_success'), 'success');

                        Ext.MessageBox.confirm(t('info'), t('reload_pimcore_changes'), function (buttonValue) {
                            if (buttonValue === 'yes') {
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

    elementChanged: function (el, newValue, oldValue, eOpts) {
        var elements = this.panel.query('[name="' + el.getName() + '"]');

        if (elements) {
            Ext.each(elements, function (element) {
                element.removeCls(this.getInheritanceClass());
                element.addCls(this.getClass(el.getName(), true));
            }.bind(this));
        }
    },

    checkForInheritance: function (element) {
        var me = this;

        if (coreshop.settings.multishop) {
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

    getConfigFormForShop: function (shopId) {

        var me = this,
            shopPanel,
            store = pimcore.globalmanager.get('coreshop_stores'),
            shop = store.getById(shopId);

        if (!shop) {
            alert('SHOP NOT FOUND!');
            return;
        }
        shopPanel = Ext.create('Ext.form.Panel', {
            title: shop.get('name'),
            border: false,
            autoScroll: true,
            forceLayout: true,
            defaults: {
                forceLayout: true,
                listeners: {
                    render: function (el) {
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
                    title: t('coreshop_base'),
                    collapsible: true,
                    collapsed: true,
                    autoHeight: true,
                    labelWidth: 250,
                    defaultType: 'textfield',
                    defaults: {width: 600},
                    items: [
                        {
                            fieldLabel: t('coreshop_base_catalogmode'),
                            xtype: 'checkbox',
                            name: 'system.catalog.mode',
                            checked: this.getValue(shopId, 'system.catalog.mode')
                        },
                        {
                            fieldLabel: t('coreshop_base_guestcheckout'),
                            xtype: 'checkbox',
                            name: 'system.guest.checkout',
                            checked: this.getValue(shopId, 'system.guest.checkout')
                        }
                    ]
                },
                /*{
                 xtype: 'fieldset',
                 title: t('coreshop_messaging'),
                 collapsible: true,
                 collapsed: true,
                 autoHeight: true,
                 labelWidth: 250,
                 defaultType: 'textfield',
                 defaults: { width: 600 },
                 items: [
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
                 },*/
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
                            name: 'system.stock.default_out_of_stock.behaviour',
                            value: this.getValue(shopId, 'system.stock.default_out_of_stock.behaviour'),
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
                            fieldLabel: t('coreshop_taxation_address'),
                            name: 'system.taxation.address',
                            value: this.getValue(shopId, 'system.taxation.address') ? this.getValue(shopId, 'system.taxation.address') : 'shipping',
                            width: 500,
                            xtype: 'combo',
                            store: [['shipping', t('coreshop_taxation_address_shipping')], ['invoice', t('coreshop_taxation_address_invoice')]],
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
                            name: 'system.prices.gross',
                            checked: this.getValue(shopId, 'system.prices.gross')
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
                            fieldLabel: t('coreshop_shipment_create'),
                            xtype: 'checkbox',
                            name: 'system.shipment.create',
                            checked: this.getValue(shopId, 'system.shipment.create')
                        },
                        {
                            fieldLabel: t('coreshop_prefix'),
                            name: 'system.shipment.prefix',
                            value: this.getValue(shopId, 'system.shipment.prefix')
                        },
                        {
                            fieldLabel: t('coreshop_suffix'),
                            name: 'system.shipment.suffix',
                            value: this.getValue(shopId, 'system.shipment.suffix')
                        },
                        {
                            fieldLabel: t('coreshop_wkhtmltopdf_params'),
                            name: 'system.shipment.wkhtml',
                            value: this.getValue(shopId, 'system.shipment.wkhtml')
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
                    defaults: {minWidth: 600},
                    items: [
                        new coreshop.object.elementHref({
                            id: me.getValue(shopId, 'system.product.fallback_image'),
                            type: 'asset',
                            subtype: 'image'
                        }, {
                            assetsAllowed: true,
                            assetTypes: [{
                                assetTypes: 'image'
                            }],
                            name: 'system.product.fallback_image',
                            title: t('coreshop_default_image')
                        }).getLayoutEdit()
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
                    defaults: {minWidth: 600},
                    items: [
                        new coreshop.object.elementHref({
                            id: me.getValue(shopId, 'system.category.fallback_image'),
                            type: 'asset',
                            subtype: 'image'
                        }, {
                            assetsAllowed: true,
                            assetTypes: [{
                                assetTypes: 'image'
                            }],
                            name: 'system.category.fallback_image',
                            title: t('coreshop_default_image')
                        }).getLayoutEdit(),
                        {
                            fieldLabel: t('coreshop_category_list_mode'),
                            name: 'system.category.list.mode',
                            value: this.getValue(shopId, 'system.category.list.mode'),
                            width: 500,
                            xtype: 'combo',
                            store: [['list', t('coreshop_category_list_mode_list')], ['grid', t('coreshop_category_list_mode_grid')]],
                            triggerAction: 'all',
                            typeAhead: false,
                            editable: false,
                            forceSelection: true,
                            queryMode: 'local'
                        },
                        {
                            xtype: 'tagfield',
                            fieldLabel: t('coreshop_category_list_per_page'),
                            store: new Ext.data.ArrayStore({
                                fields: [
                                    'perPage'
                                ],
                                data: []
                            }),
                            value: this.getValue(shopId, 'system.category.list.per_page'),
                            name: 'system.category.list.per_page',
                            createNewOnEnter: true,
                            createNewOnBlur: true,
                            queryMode: 'local',
                            displayField: 'perPage',
                            valueField: 'perPage',
                            hideTrigger: true
                        },
                        {
                            fieldLabel: t('coreshop_category_list_per_page_default'),
                            name: 'system.category.list.per_page.default',
                            xtype: 'numberfield',
                            minValue: 1,
                            value: this.getValue(shopId, 'system.category.list.per_page.default')
                        },
                        {
                            xtype: 'tagfield',
                            fieldLabel: t('coreshop_category_grid_per_page'),
                            store: new Ext.data.ArrayStore({
                                fields: [
                                    'perPage'
                                ],
                                data: []
                            }),
                            value: this.getValue(shopId, 'system.category.grid.per_page'),
                            name: 'system.category.grid.per_page',
                            createNewOnEnter: true,
                            createNewOnBlur: true,
                            queryMode: 'local',
                            displayField: 'perPage',
                            valueField: 'perPage',
                            hideTrigger: true
                        },
                        {
                            fieldLabel: t('coreshop_category_grid_per_page_default'),
                            name: 'system.category.grid.per_page.default',
                            xtype: 'numberfield',
                            minValue: 1,
                            value: this.getValue(shopId, 'system.category.grid.per_page.default')
                        },
                        {
                            fieldLabel: t('coreshop_category_variant_mode'),
                            name: 'system.category.variant_mode',
                            value: this.getValue(shopId, 'system.category.variant_mode'),
                            width: 500,
                            xtype: 'combo',
                            store: [['hide', t('coreshop_category_variant_mode_hide')], ['include', t('coreshop_category_variant_mode_include')], ['include_parent_object', t('coreshop_category_variant_mode_include_parent_object')]],
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
                    title: t('coreshop_order'),
                    collapsible: true,
                    collapsed: true,
                    autoHeight: true,
                    labelWidth: 250,
                    defaultType: 'textfield',
                    defaults: {width: 600},
                    items: [
                        {
                            fieldLabel: t('coreshop_prefix'),
                            name: 'system.order.prefix',
                            value: this.getValue(shopId, 'system.order.prefix')
                        },
                        {
                            fieldLabel: t('coreshop_suffix'),
                            name: 'system.order.suffix',
                            value: this.getValue(shopId, 'system.order.suffix')
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
                            name: 'system.invoice.create',
                            checked: this.getValue(shopId, 'system.invoice.create')
                        },
                        {
                            fieldLabel: t('coreshop_prefix'),
                            name: 'system.invoice.prefix',
                            value: this.getValue(shopId, 'system.invoice.prefix')
                        },
                        {
                            fieldLabel: t('coreshop_suffix'),
                            name: 'system.invoice.suffix',
                            value: this.getValue(shopId, 'system.invoice.suffix')
                        },
                        {
                            fieldLabel: t('coreshop_wkhtmltopdf_params'),
                            name: 'system.invoice.wktml',
                            value: this.getValue(shopId, 'system.invoice.wkhtml')
                        }
                    ]
                }
                /*{ //TODO: Use Symfony Configuration for this?!
                 xtype: 'fieldset',
                 title: t('coreshop_cart'),
                 collapsible: true,
                 collapsed: true,
                 autoHeight: true,
                 labelWidth: 250,
                 defaultType: 'textfield',
                 defaults: { width: 600 },
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
                 }*/
            ]
        });

        return shopPanel;
    }
});
