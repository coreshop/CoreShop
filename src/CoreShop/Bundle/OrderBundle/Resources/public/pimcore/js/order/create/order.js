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

pimcore.registerNS('coreshop.order.order.create.order');
coreshop.order.order.create.order = Class.create({

    borderStyle: {
        borderStyle: 'solid',
        borderColor: '#ccc',
        borderRadius: '5px',
        borderWidth: '1px'
    },
    customerId: null,
    currency: {
        symbol: '€'
    },
    currencies: [],
    currenciesStore: null,
    addressStore: null,

    initialize: function (customerId) {
        this.customerId = customerId;
        this.currencies = pimcore.globalmanager.get("coreshop_currencies").getRange().filter(function (record) {
            return record.get("active");
        }).map(function (record) {
            return record.data;
        });
        this.currenciesStore = new Ext.data.JsonStore({
            data: this.currencies
        });
        this.carriersStore = new Ext.data.JsonStore({
            data: []
        });

        if (this.currencies.length > 0) {
            this.currency = this.currencies[0];

            Ext.Ajax.request({
                url: '/admin/coreshop/order/get-customer-details',
                method: 'post',
                params: {
                    customerId: customerId
                },
                callback: function (request, success, response) {
                    try {
                        response = Ext.decode(response.responseText);

                        if (response.success) {
                            this.customer = response.customer;

                            var modelName = 'CoreShopCreateOrderAddress';
                            if (!Ext.ClassManager.isCreated(modelName)) {
                                Ext.define(modelName, {
                                    extend: 'Ext.data.Model',
                                    idProperty: 'o_id'
                                });
                            }

                            this.addressStore = new Ext.data.JsonStore({
                                data: this.customer.addresses,
                                model: modelName
                            });

                            this.getLayout();
                        } else {
                            Ext.Msg.alert(t('error'), response.message);
                        }
                    }
                    catch (e) {
                        Ext.Msg.alert(t('error'), e);
                    }
                }.bind(this)
            });
        }
        else {
            Ext.Msg.alert(t('error'), t('coreshop_no_currencies'));
        }
    },

    getLayout: function () {
        if (!this.layout) {

            this.layoutId = Ext.id();

            // create new panel
            this.layout = new Ext.panel.Panel({
                id: this.layoutId,
                title: t('coreshop_order_create'),
                iconCls: 'coreshop_icon_order_create',
                border: false,
                layout: 'border',
                autoScroll: true,
                closable: true,
                items: [this.getPanel()]
            });

            // add panel to pimcore panel tabs
            var tabPanel = Ext.getCmp('pimcore_panel_tabs');
            tabPanel.add(this.layout);
            tabPanel.setActiveItem(this.layoutId);

            // update layout
            pimcore.layout.refresh();
        }

        return this.layout;
    },

    getPanel: function () {
        var defaults = {
            style: this.borderStyle,
            cls: 'coreshop-panel',
            bodyPadding: 5
        };

        var items = [
            this.getCustomerPanel(),
            this.getCartPanel(),
            this.getAddressPanel(),
            this.getDeliveryPanel(),
            this.getTotalPanel()
        ];

        this.panel = Ext.create('Ext.container.Container', {
            border: false,
            items: items,
            padding: 20,
            region: 'center',
            defaults: defaults
        });

        return this.panel;
    },

    getCustomerPanel: function () {
        if (!this.customerPanel) {
            //pimcore.object.tags.href
            var cartsAndOrders = Ext.create('Ext.tab.Panel', {
                items: [
                    this.getCustomerCartsGrid(),
                    this.getCustomerOrdersGrid()
                ]
            });

            this.customerPanel = Ext.create('Ext.panel.Panel', {
                title: t('coreshop_customer'),
                margin: '0 20 20 0',
                border: true,
                flex: 8,
                iconCls: 'coreshop_icon_customers',
                items: [
                    cartsAndOrders
                ]
            });
        }

        return this.customerPanel;
    },

    getCustomerCartsGrid: function () {
        if (!this.customerCartsGrid) {
            this.customerCartGridStore = new Ext.data.JsonStore({
                proxy: {
                    type: 'ajax',
                    url: '/admin/coreshop/order/get-customer-carts',
                    reader: {
                        type: 'json',
                        rootProperty: 'carts'
                    },
                    extraParams: {
                        customerId: this.customerId
                    }
                }
            });
            this.customerCartGridStore.load();

            this.customerCartsGrid = Ext.create('Ext.grid.Panel', {
                xtype: 'grid',
                margin: '0 0 15 0',
                cls: 'coreshop-detail-grid',
                store: this.customerCartGridStore,
                title: t('coreshop_customer_carts'),
                iconCls: 'coreshop_icon_cart',
                columns: [
                    {
                        xtype: 'gridcolumn',
                        flex: 1,
                        dataIndex: 'date',
                        text: t('date'),
                        renderer: function (value, metaData, record) {
                            return Ext.Date.format(new Date(value * 1000), t('coreshop_date_time_format'));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        flex: 1,
                        dataIndex: 'name',
                        text: t('name')
                    },
                    {
                        xtype: 'gridcolumn',
                        dataIndex: 'total',
                        width: 150,
                        align: 'right',
                        text: t('coreshop_total'),
                        renderer: function (value, metaData, record) {
                            return '<span style="font-weight:bold">' + coreshop.util.format.currency(record.get("currency").symbol, value) + '</span>';
                        }.bind(this)
                    },
                    {
                        menuDisabled: true,
                        sortable: false,
                        xtype: 'actioncolumn',
                        width: 50,
                        items: [{
                            iconCls: 'pimcore_icon_arrow_right',
                            tooltip: t('coreshop_use_for_order'),
                            handler: function (grid, rowIndex) {
                                var record = grid.getStore().getAt(rowIndex);

                                this.addProductsToCart(record.get("productIds"), true);
                            }.bind(this)
                        }]
                    }
                ]
            });
        }

        return this.customerCartsGrid;
    },

    getCustomerOrdersGrid: function () {
        if (!this.customerOrdersGrid) {
            this.customerOrdersGridStore = new Ext.data.JsonStore({
                proxy: {
                    type: 'ajax',
                    url: '/admin/coreshop/order/get-customer-orders',
                    reader: {
                        type: 'json',
                        rootProperty: 'orders'
                    },
                    extraParams: {
                        customerId: this.customerId
                    }
                }
            });
            this.customerOrdersGridStore.load();

            this.customerOrdersGrid = Ext.create('Ext.grid.Panel', {
                xtype: 'grid',
                margin: '0 0 15 0',
                cls: 'coreshop-detail-grid',
                title: t('coreshop_customer_orders'),
                iconCls: 'coreshop_icon_orders',
                store: this.customerOrdersGridStore,
                columns: [
                    {
                        xtype: 'gridcolumn',
                        flex: 1,
                        dataIndex: 'date',
                        text: t('date'),
                        renderer: function (value, metaData, record) {
                            return Ext.Date.format(new Date(value * 1000), t('coreshop_date_time_format'));
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        dataIndex: 'total',
                        width: 150,
                        align: 'right',
                        text: t('coreshop_total'),
                        renderer: function (value, metaData, record) {
                            return '<span style="font-weight:bold">' + coreshop.util.format.currency(record.get("currency").symbol, value) + '</span>';
                        }.bind(this)
                    },
                    {
                        menuDisabled: true,
                        sortable: false,
                        xtype: 'actioncolumn',
                        width: 50,
                        items: [{
                            iconCls: 'pimcore_icon_arrow_right',
                            tooltip: t('coreshop_use_for_order'),
                            handler: function (grid, rowIndex) {
                                var record = grid.getStore().getAt(rowIndex);

                                this.addProductsToCart(record.get("productIds"), true);
                            }.bind(this)
                        }
                        ]
                    }
                    //TODO: Add open button?
                ]
            });
        }

        return this.customerOrdersGrid;
    },

    getCartPanel: function () {
        if (!this.cartPanel) {
            var modelName = 'CoreShopCreateOrderCart';
            if (!Ext.ClassManager.isCreated(modelName)) {
                Ext.define(modelName, {
                    extend: 'Ext.data.Model',
                    idProperty: 'o_id'
                });
            }

            this.cartPanelStore = new Ext.data.JsonStore({
                data: [],
                model: modelName
            });

            var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
                listeners: {
                    edit: function (editor, context, eOpts) {
                        if (context.originalValue != context.value) {
                            this.cartPanelGrid.getView().refresh();
                        }

                        this.reloadCarriers();
                        this.reloadTotalPanel();
                    }.bind(this)
                }
            });

            this.cartPanelCurrency = new Ext.form.ComboBox({
                fieldLabel: t('coreshop_currency'),
                name: 'currency',
                width: 500,
                store: this.currenciesStore,
                triggerAction: 'all',
                typeAhead: false,
                editable: false,
                forceSelection: true,
                queryMode: 'local',
                valueField: 'id',
                value: this.currency.id,
                displayTpl: Ext.create('Ext.XTemplate', '<tpl for=".">', '{name} ({symbol})', '</tpl>'),
                listConfig: {
                    itemTpl: Ext.create('Ext.XTemplate', '', '{name} ({symbol})', '')
                },
                listeners: {
                    change: function (combo, value) {
                        this.currency = combo.getStore().getById(value).data;

                        this.refreshCart();
                    }.bind(this)
                }
            });

            var languageStore = [];
            var websiteLanguages = pimcore.settings.websiteLanguages;

            for (var i = 0; i < websiteLanguages.length; i++) {
                languageStore.push([websiteLanguages[i], pimcore.available_languages[websiteLanguages[i]] + " [" + websiteLanguages[i] + "]"]);
            }

            this.cartPanelLanguage = new Ext.form.ComboBox({
                fieldLabel: t('language'),
                name: "language",
                store: languageStore,
                editable: false,
                triggerAction: 'all',
                mode: "local",
                width: 500,
                emptyText: t('language'),
                value: languageStore[0]
            });

            this.cartPanelGrid = Ext.create('Ext.grid.Panel', {
                margin: '0 0 15 0',
                cls: 'coreshop-detail-grid',
                store: this.cartPanelStore,
                plugins: [cellEditing],
                columns: [
                    {
                        xtype: 'gridcolumn',
                        dataIndex: 'o_id',
                        text: t('id'),
                        width: 100
                    },
                    {
                        xtype: 'gridcolumn',
                        dataIndex: 'name',
                        flex: 1,
                        text: t('name'),
                        renderer: function (value, metaData, record) {
                            if (Object.keys(record.get("localizedfields").data).indexOf(pimcore.settings.language) > 0)
                                return record.get("localizedfields").data[pimcore.settings.language].name;
                            else {
                                var keys = Object.keys(record.get("localizedfields").data);

                                if (keys.length > 0) {
                                    return record.get("localizedfields").data[keys[0]].name;
                                }
                            }

                            return "";
                        }.bind(this)
                    },
                    {
                        xtype: 'gridcolumn',
                        dataIndex: 'price',
                        width: 150,
                        align: 'right',
                        text: t('coreshop_price'),
                        renderer: function (value, metaData, record) {
                            return '<span style="font-weight:bold">' + coreshop.util.format.currency(this.currency.symbol, value) + '</span>';
                        }.bind(this)
                        /*field : { TODO: Make price editable
                         xtype: 'numberfield',
                         decimalPrecision : 2
                         }*/
                    },
                    {
                        xtype: 'gridcolumn',
                        dataIndex: 'amount',
                        width: 100,
                        text: t('coreshop_amount'),
                        field: {
                            xtype: 'numberfield',
                            decimalPrecision: 0
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        dataIndex: 'total',
                        width: 150,
                        align: 'right',
                        text: t('coreshop_total'),
                        renderer: function (value, metaData, record) {
                            var price = record.get("price");
                            var amount = record.get("amount");
                            var total = price * amount;

                            return '<span style="font-weight:bold">' + coreshop.util.format.currency(this.currency.symbol, total) + '</span>';
                        }.bind(this)
                    }
                ]
            });

            this.cartPanel = Ext.create('Ext.panel.Panel', {
                title: t('coreshop_cart'),
                margin: '0 20 20 0',
                border: true,
                flex: 8,
                iconCls: 'coreshop_icon_cart',
                tools: [
                    {
                        type: 'coreshop-add-product',
                        tooltip: t('add'),
                        handler: function () {
                            pimcore.helpers.itemselector(
                                true,
                                function (products) {
                                    products = products.map(function (pr) {
                                        return {id: pr.id, amount: 1};
                                    });

                                    this.addProductsToCart(products);
                                }.bind(this),
                                {
                                    type: ['object'],
                                    subtype: {
                                        object: ['object', 'variant']
                                    },
                                    specific: {
                                        classes: [coreshop.settings.classMapping.product]
                                    }
                                }
                            );
                        }.bind(this)
                    }
                ],
                items: [
                    this.cartPanelGrid,
                    this.cartPanelCurrency,
                    this.cartPanelLanguage
                ]
            });
        }

        return this.cartPanel;
    },

    addProductsToCart: function (products, reset) {
        this.cartPanel.setLoading(t("loading"));

        Ext.Ajax.request({
            url: '/admin/coreshop/order/get-product-details',
            method: 'post',
            params: {
                'products': Ext.JSON.encode(products),
                'currency': this.currency.id
            },
            callback: function (request, success, response) {
                try {
                    response = Ext.decode(response.responseText);

                    if (response.success) {
                        if (reset) {
                            this.cartPanelStore.removeAll();
                        }

                        this.cartPanelStore.add(response.products);

                        this.reloadCarriers();
                        this.reloadTotalPanel();
                    } else {
                        Ext.Msg.alert(t('error'), response.message);
                    }
                }
                catch (e) {
                    Ext.Msg.alert(t('error'), e);
                }

                this.cartPanel.setLoading(false);
            }.bind(this)
        });
    },

    getCartProducts: function () {
        return this.cartPanelStore.getRange().map(function (record) {
            return {
                id: record.get("o_id"),
                amount: record.get("amount")
            }
        });
    },

    refreshCart: function () {
        this.addProductsToCart(this.getCartProducts(), true);
    },

    getDiscountPanel: function () {
        //TODO:
    },

    getAddressPanel: function () {
        if (!this.addressPanel) {
            this.addressPanel = Ext.create('Ext.panel.Panel', {
                title: t('coreshop_address'),
                margin: '0 20 20 0',
                border: true,
                flex: 8,
                iconCls: 'coreshop_icon_address',
                layout: 'hbox',
                items: [
                    this.getAddressPanelForType('shipping'),
                    this.getAddressPanelForType('billing')
                ]
            });
        }

        return this.addressPanel;
    },

    getAddressPanelForType: function (type) {
        var key = "addressPanel" + type;
        var addressKey = "address" + type;

        if (!this[key]) {
            var addressDetailPanelKey = "addressDetailPanel" + type;

            this[addressDetailPanelKey] = Ext.create('Ext.panel.Panel', {});

            this[key] = Ext.create("Ext.panel.Panel", {
                flex: 1,
                padding: 10,
                items: [
                    {
                        xtype: 'combo',
                        fieldLabel: t('coreshop_address_' + type),
                        labelWidth: 150,
                        name: "address" + type,
                        store: this.addressStore,
                        editable: false,
                        triggerAction: 'all',
                        queryMode: "local",
                        width: 500,
                        displayField: 'name',
                        valueField: 'o_id',
                        listeners: {
                            change: function (combo, value) {
                                var address = this.addressStore.getById(value);

                                this[addressDetailPanelKey].removeAll();
                                this[addressDetailPanelKey].add(this.getAddressPanelForAddress(address.data));

                                this[addressKey] = address.data;

                                this.reloadCarriers();
                            }.bind(this)
                        }
                    },
                    this[addressDetailPanelKey]
                ]
            });
        }

        return this[key];
    },

    getAddressPanelForAddress: function (address) {
        var country = pimcore.globalmanager.get("coreshop_countries").getById(address.country);

        var panel = {
            xtype: 'panel',
            dockedItems: [{
                xtype: 'toolbar',
                dock: 'top',
                items: [
                    '->',
                    {
                        iconCls: 'coreshop_icon_open',
                        text: t('open'),
                        handler: function () {
                            pimcore.helpers.openObject(address.o_id);
                        }.bind(this)
                    }
                ]
            }],
            layout: {
                type: 'hbox',
                align: 'stretch'
            },
            height: 220,
            items: [
                {
                    xtype: 'panel',
                    bodyPadding: 5,
                    html: (address.firstname ? address.firstname : '') + ' ' + (address.lastname ? address.lastname : '') + '<br/>' +
                    (address.company ? address.company + '<br/>' : '') +
                    (address.street ? address.street : '') + ' ' + (address.nr ? address.nr : '') + '<br/>' +
                    (address.zip ? address.zip : '') + ' ' + (address.city ? address.city : '') + '<br/>' +
                    (country ? country.get("name") : ''),
                    flex: 1
                }
            ]
        };

        if (pimcore.settings.google_maps_api_key) {
            panel.items.push({
                xtype: 'panel',
                html: '<img src="https://maps.googleapis.com/maps/api/staticmap?zoom=13&size=200x200&maptype=roadmap'
                + '&center=' + address.street + '+' + address.nr + '+' + address.zip + '+' + address.city + '+' + (country ? country.get("name") : '')
                + '&markers=color:blue|' + address.street + '+' + address.nr + '+' + address.zip + '+' + address.city + '+' + (country ? country.get("name") : '')
                + '&key=' + pimcore.settings.google_maps_api_key
                + '" />',
                flex: 1,
                bodyPadding: 5
            });
        }

        return panel;
    },

    getDeliveryPanel: function () {
        if (!this.deliveryPanel) {
            this.deliveryPanelCarrier = Ext.create({
                xtype: 'combo',
                fieldLabel: t('coreshop_carrier'),
                name: "carrier",
                store: this.carriersStore,
                editable: false,
                triggerAction: 'all',
                queryMode: "local",
                width: 500,
                displayField: 'name',
                valueField: 'id',
                listeners: {
                    change: function (combo, value) {
                        var carrier = this.carriersStore.getById(value);

                        this.deliveryPanelPriceField.setValue(coreshop.util.format.currency(this.currency.symbol, carrier.get("price")));

                        this.reloadTotalPanel();
                    }.bind(this)
                }
            });

            this.deliveryPanelPriceField = Ext.create({
                xtype: 'textfield',
                value: coreshop.util.format.currency(this.currency.symbol, 0),
                disabled: true,
                fieldLabel: t('coreshop_price')
            });

            this.deliveryPanelFreeShipping = Ext.create({
                xtype: 'checkbox',
                name: 'free',
                fieldLabel: t('coreshop_free_shipping'),
                listeners: {
                    change: function (cb, value) {
                        this.reloadTotalPanel();
                    }.bind(this)
                }
            });

            this.deliveryPanel = Ext.create('Ext.panel.Panel', {
                title: t('coreshop_shipping'),
                margin: '0 20 20 0',
                border: true,
                flex: 8,
                iconCls: 'coreshop_icon_carriers',
                layout: 'vbox',
                hidden: true,
                items: [
                    this.deliveryPanelCarrier, this.deliveryPanelPriceField, this.deliveryPanelFreeShipping
                ]
            });
        }

        return this.deliveryPanel;
    },

    reloadCarriers: function () {
        if (this.addressshipping && this.addressbilling && this.cartPanelStore.count() > 0) {
            this.deliveryPanel.show();
            this.deliveryPanel.setLoading(t("loading"));

            Ext.Ajax.request({
                url: '/admin/coreshop/order/get-carriers-details',
                method: 'post',
                params: {
                    customerId: this.customerId,
                    products: Ext.JSON.encode(this.getCartProducts()),
                    shippingAddress: this.addressshipping.o_id,
                    billingAddress: this.addressbilling.o_id,
                    currency: this.currency.id,
                    language: this.cartPanelLanguage.getValue()
                },
                callback: function (request, success, response) {
                    try {
                        response = Ext.decode(response.responseText);

                        if (response.success) {
                            this.carriersStore.loadData(response.carriers);
                        } else {
                            Ext.Msg.alert(t('error'), response.message);
                        }
                    }
                    catch (e) {
                        Ext.Msg.alert(t('error'), e);
                    }

                    this.deliveryPanel.setLoading(false);
                }.bind(this)
            });
        } else {
            this.deliveryPanel.hide();
        }
    },

    getTotalPanel: function () {
        if (!this.totalPanel) {
            this.totalStore = new Ext.data.JsonStore({
                data: []
            });

            var paymentProvidersStore = new Ext.data.Store({
                proxy: {
                    type: 'ajax',
                    url: '/admin/coreshop/order/get-payment-providers',
                    reader: {
                        type: 'json',
                        rootProperty: 'data'
                    }
                },
                fields: ['id', 'name']
            });
            paymentProvidersStore.load();

            this.totalPanelPaymentModule = Ext.create({
                xtype: 'combo',
                fieldLabel: t('coreshop_paymentProvider'),
                typeAhead: true,
                mode: 'local',
                listWidth: 100,
                store: paymentProvidersStore,
                displayField: 'name',
                valueField: 'id',
                triggerAction: 'all',
                labelWidth: 150
            });

            this.totalPanelShop = Ext.create({
                xtype: 'combo',
                fieldLabel: t('coreshop_shop'),
                store: pimcore.globalmanager.get('coreshop_stores'),
                displayField: 'name',
                valueField: 'id',
                triggerAction: 'all',
                typeAhead: false,
                editable: false,
                forceSelection: true,
                queryMode: 'local',
                hidden: !coreshop.settings.multishop,
                value: 1,
                labelWidth: 150
            });

            this.totalPanel = Ext.create('Ext.panel.Panel', {
                title: t('coreshop_total'),
                margin: '0 20 20 0',
                border: false,
                iconCls: 'coreshop_icon_orders',
                hidden: true,
                items: [
                    {
                        xtype: 'grid',
                        store: this.totalStore,
                        hideHeaders: true,
                        margin: '0 0 20 0',
                        columns: [
                            {
                                xtype: 'gridcolumn',
                                dataIndex: 'key',
                                flex: 1,
                                renderer: function (value, metaData, record) {
                                    return '<span style="font-weight:bold">' + t('coreshop_' + value) + '</span>';
                                }
                            },
                            {
                                xtype: 'gridcolumn',
                                dataIndex: 'value',
                                width: 150,
                                align: 'right',
                                renderer: function (value) {
                                    return '<span style="font-weight:bold">' + coreshop.util.format.currency(this.currency.symbol, value) + '</span>';
                                }.bind(this)
                            }
                        ]
                    },
                    this.totalPanelPaymentModule,
                    this.totalPanelShop
                ],
                dockedItems: [{
                    xtype: 'toolbar',
                    dock: 'bottom',
                    items: [
                        '->',
                        {
                            iconCls: 'pimcore_icon_save',
                            text: t('create'),
                            handler: function () {
                                this.createOrder();
                            }.bind(this)
                        }
                    ]
                }]
            });
        }

        return this.totalPanel;
    },

    reloadTotalPanel: function () {
        if (this.addressshipping && this.addressbilling && this.cartPanelStore.count() > 0 && this.deliveryPanelCarrier.getValue()) {
            this.totalPanel.setLoading(t("loading"));

            Ext.Ajax.request({
                url: '/admin/coreshop/order/get-order-total',
                method: 'post',
                params: {
                    customerId: this.customerId,
                    products: Ext.JSON.encode(this.getCartProducts()),
                    shippingAddress: this.addressshipping.o_id,
                    billingAddress: this.addressbilling.o_id,
                    currency: this.currency.id,
                    language: this.cartPanelLanguage.getValue(),
                    carrier: this.deliveryPanelCarrier.getValue(),
                    freeShipping: this.deliveryPanelFreeShipping.getValue() ? 1 : 0
                },
                callback: function (request, success, response) {
                    try {
                        response = Ext.decode(response.responseText);

                        if (response.success) {
                            this.totalStore.loadData(response.summary);
                        } else {
                            Ext.Msg.alert(t('error'), response.message);
                        }
                    }
                    catch (e) {
                        Ext.Msg.alert(t('error'), e);
                    }

                    this.totalPanel.setLoading(false);
                }.bind(this)
            });

            this.totalPanel.show();
        }
        else {
            this.totalPanel.hide();
        }
    },

    createOrder: function () {
        this.layout.setLoading(t("coreshop_creating_order"));

        Ext.Ajax.request({
            url: '/admin/coreshop/order/create-order',
            method: 'post',
            params: this.getParams(),
            callback: function (request, success, response) {
                try {
                    response = Ext.decode(response.responseText);

                    if (response.success) {
                        coreshop.helpers.openOrder(response.orderId);
                    } else {
                        Ext.Msg.alert(t('error'), response.message);
                    }
                }
                catch (e) {
                    Ext.Msg.alert(t('error'), e);
                }

                this.layout.setLoading(false);
            }.bind(this)
        });
    },

    getParams: function () {
        return {
            customerId: this.customerId,
            products: Ext.JSON.encode(this.getCartProducts()),
            shippingAddress: this.addressshipping.o_id,
            billingAddress: this.addressbilling.o_id,
            currency: this.currency.id,
            language: this.cartPanelLanguage.getValue(),
            carrier: this.deliveryPanelCarrier.getValue(),
            freeShipping: this.deliveryPanelFreeShipping.getValue() ? 1 : 0,
            paymentProvider: this.totalPanelPaymentModule.getValue(),
            shop: this.totalPanelShop.getValue()
        };
    }
});
