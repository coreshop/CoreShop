/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.core.settings');
coreshop.core.settings = Class.create({

    shopPanels: {},

    initialize: function () {

        this.getData();
    },

    getData: function () {
        Ext.Ajax.request({
            url: '/admin/coreshop/configurations/get-all',
            success: function (response) {

                this.data = Ext.decode(response.responseText).data;

                this.loadStores();

            }.bind(this)
        });
    },

    loadStores: function()
    {
        this.stores = Ext.create('store.coreshop_stores').load(function() {
            this.getTabPanel();
        }.bind(this));
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
            shop = this.stores.getById(shopId);

        if (!shop) {
            alert('STORE NOT FOUND!');
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
                            fieldLabel: t('coreshop_base_guestcheckout'),
                            xtype: 'checkbox',
                            name: 'system.guest.checkout',
                            checked: this.getValue(shopId, 'system.guest.checkout')
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
                    defaults: {minWidth: 600},
                    items: [
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
                            fieldLabel: t('coreshop_category_list_include_subcategories'),
                            name: 'system.category.list.include_subcategories',
                            xtype: 'checkbox',
                            checked: this.getValue(shopId, 'system.category.list.include_subcategories')
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
                    title: t('coreshop_quote'),
                    collapsible: true,
                    collapsed: true,
                    autoHeight: true,
                    labelWidth: 250,
                    defaultType: 'textfield',
                    defaults: {width: 600},
                    items: [
                        {
                            fieldLabel: t('coreshop_prefix'),
                            name: 'system.quote.prefix',
                            value: this.getValue(shopId, 'system.quote.prefix')
                        },
                        {
                            fieldLabel: t('coreshop_suffix'),
                            name: 'system.quote.suffix',
                            value: this.getValue(shopId, 'system.quote.suffix')
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
                            name: 'system.invoice.wkhtml',
                            value: this.getValue(shopId, 'system.invoice.wkhtml')
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
                }
            ]
        });

        coreshop.broker.fireEvent('coreShop.settings.store', this, shopId, shopPanel);

        return shopPanel;
    }
});
