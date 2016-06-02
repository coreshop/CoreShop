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

pimcore.registerNS('pimcore.plugin.coreshop.orders.order');
pimcore.plugin.coreshop.orders.order = Class.create({

    order : null,
    layoutId : null,

    borderStyle : {
        borderStyle: 'solid',
        borderColor: '#ccc',
        borderRadius: '5px',
        borderWidth : '1px'
    },

    initialize: function (order) {
        this.order = order;
        this.layoutId ="coreshop_order_" + this.order.o_id;

        this.getLayout();
    },

    activate: function () {
        var tabPanel = Ext.getCmp('pimcore_panel_tabs');
        tabPanel.setActiveItem(this.layoutId);
    },

    getLayout: function () {
        if (!this.layout) {

            // create new panel
            this.layout = new Ext.Container({
                id: this.layoutId,
                title: t('coreshop_order') + ': ' + this.order.orderNumber,
                iconCls: 'coreshop_icon_orders',
                border: false,
                layout: 'border',
                autoScroll: true,
                closable: true,
                items: this.getItems()
            });

            // add event listener
            this.layout.on('destroy', function () {
                pimcore.globalmanager.remove(this.layoutId);
            }.bind(this));

            // add panel to pimcore panel tabs
            var tabPanel = Ext.getCmp('pimcore_panel_tabs');
            tabPanel.add(this.layout);
            tabPanel.setActiveItem(this.layoutId);

            // update layout
            pimcore.layout.refresh();
        }

        return this.layout;
    },

    getItems: function () {
        return [this.getPanel()];
    },

    getPanel : function () {
        var defaults = {
            style: this.borderStyle,
            cls : 'coreshop-order',
            bodyPadding : 5
        };

        var leftItems = [
            this.getOrderInfo(),
            this.getShippingInfo(),
            this.getPaymentInfo()
        ];

        var rightItems = [
            this.getCustomerInfo(),
            this.getMessagesInfo(),
        ];

        var items = [
            this.getHeader(),
            {
                xtype : 'container',
                layout : 'hbox',
                margin : '0 0 20 0',
                border : 0,
                style: {
                    border : 0
                },
                items : [
                    {
                        xtype : 'container',
                        border : 0,
                        style : {
                            border : 0
                        },
                        flex : 7,
                        defaults : defaults,
                        items : leftItems
                    },
                    {
                        xtype : 'container',
                        border : 0,
                        style : {
                            border : 0
                        },
                        flex : 5,
                        defaults : defaults,
                        items : rightItems
                    }
                ]
            },

            this.getDetailInfo()
        ];


        var pluginPanel = this.getPluginInfo();

        if(pluginPanel) {
            this.items.push(pluginPanel);
        }

        this.panel = Ext.create('Ext.container.Container', {
            border : false,
            items : items,
            padding : 20,
            region : 'center',
            defaults : defaults
        });


        return this.panel;
    },

    getHeader : function() {
        if(!this.headerPanel) {
            this.headerPanel = Ext.create('Ext.panel.Panel', {
                layout : 'hbox',
                margin : '0 0 20 0',
                items : [
                    {
                        xtype : 'panel',
                        html : t('coreshop_date') + '<br/><span class="coreshop_order_big">' + Ext.Date.format(new Date(this.order.o_creationDate * 1000), 'Y-m-d H:i:s') + "</span>",
                        bodyPadding : 20,
                        flex : 1
                    },
                    {
                        xtype : 'panel',
                        html : t('coreshop_orders_total') + '<br/><span class="coreshop_order_big">' + coreshop.util.format.currency(this.order.currency.symbol, this.order.total) + "</span>",
                        bodyPadding : 20,
                        flex : 1
                    },
                    {
                        xtype : 'panel',
                        html : t('coreshop_messaging_messages') + '<br/><span class="coreshop_order_big">' + 0 + "</span>", //TODO: Add Messages
                        bodyPadding : 20,
                        flex : 1
                    },
                    {
                        xtype : 'panel',
                        html : t('coreshop_product_count') + '<br/><span class="coreshop_order_big">' + this.order.items.length + "</span>",
                        bodyPadding : 20,
                        flex : 1
                    }
                ]
            });
        }

        return this.headerPanel;
    },

    getOrderInfo : function() {
        if(!this.orderInfo) {
            this.orderStatesStore = new Ext.data.JsonStore({
                data : this.order.statesHistory
            });

            this.orderInfo = Ext.create('Ext.panel.Panel', {
                title : t('coreshop_order') + ": " + this.order.orderNumber + " (" + this.order.o_id + ")",
                margin : '0 20 20 0',
                border : true,
                flex : 8,
                iconCls : 'coreshop_icon_orders',
                tools : [
                    {
                        type: 'coreshop-open',
                        tooltip: t('open'),
                        handler : function() {
                            pimcore.helpers.openObject(this.order.o_id);
                        }.bind(this)
                    }
                ],
                items : [
                    {
                        xtype : 'panel',
                        style: this.borderStyle,
                        bodyPadding : 5,
                        margin: '0 0 15 0',
                        items : [{
                            xtype: 'button',
                            text : this.order.invoice ? t('coreshop_invoice') : t('coreshop_invoice_not_generated'),
                            disabled : !this.order.invoice,
                            handler : function() {
                                pimcore.helpers.openAsset(this.order.invoice.id, this.order.invoice.type);
                            }.bind(this)
                        }]
                    },
                    {
                        xtype : 'grid',
                        margin: '0 0 15 0',
                        cls : 'coreshop-order-detail-grid',
                        store : this.orderStatesStore,
                        columns : [
                            {
                                xtype : 'gridcolumn',
                                dataIndex : 'toState',
                                text : t('coreshop_orderstate'),
                                flex : 1,
                                renderer : function(value, metaData) {
                                    var store = pimcore.globalmanager.get("coreshop_orderstates");
                                    var orderState = store.getById(value);

                                    if(orderState) {
                                        var bgColor = orderState.get('color');
                                        var textColor = coreshop.helpers.constrastColor(bgColor);

                                        return '<span class="rounded-color" style="background-color:' + bgColor + '; color: ' + textColor + '">' + orderState.get('name') + '</span>';
                                    }

                                    return value;
                                }
                            },
                            {
                                xtype : 'gridcolumn',
                                flex : 1,
                                dataIndex : 'date',
                                text : t('date')
                            },
                            {
                                xtype : 'gridcolumn',
                                dataIndex : 'toState',
                                flex : 1,
                                align : 'right',
                                renderer : function(value) {
                                    var store = pimcore.globalmanager.get("coreshop_orderstates");
                                    var orderState = store.getById(value);

                                    if(orderState && orderState.get("email") === "1") {
                                        var id = Ext.id();
                                        Ext.defer(function () {
                                            Ext.widget('button', {
                                                renderTo: id,
                                                text: t('coreshop_order_resend_email'),
                                                flex : 1,
                                                handler: function () { Ext.Msg.alert('Info', orderState.get('name')) }
                                            });
                                        }, 50);
                                        return Ext.String.format('<div id="{0}"></div>', id);
                                    }

                                    return '';
                                }
                            }
                        ]
                    },
                    {
                        xtype : 'panel',
                        style: this.borderStyle,
                        bodyPadding : 5,
                        layout : 'hbox',
                        items : [
                            {
                                xtype : 'combo',
                                triggerAction: 'all',
                                editable: false,
                                typeAhead: false,
                                forceSelection: true,
                                fieldLabel: t('coreshop_orderstate'),
                                store: pimcore.globalmanager.get("coreshop_orderstates"),
                                componentCls: 'object_field',
                                flex : 1,
                                labelWidth: 100,
                                displayField:'name',
                                valueField:'id',
                                queryMode : 'local'
                            },
                            {
                                xtype : 'button',
                                text : t('coreshop_orderstate_change'),
                                handler : function(button) {
                                    var comboBox = button.previousSibling();

                                    Ext.Ajax.request({
                                        url: "/plugin/CoreShop/admin_order/change-order-state",
                                        params: {
                                            id: this.order.o_id,
                                            orderStateId : comboBox.getValue()
                                        },
                                        success: function (response) {
                                            var res = Ext.decode(response.responseText);

                                            if (res.success) {
                                                this.orderStatesStore.loadData(res.statesHistory);
                                            } else {
                                                pimcore.helpers.showNotification(t('error'), t('coreshop_save_error'), 'error');
                                            }

                                        }.bind(this)
                                    });
                                }.bind(this),
                                width : 100
                            }
                        ]
                    }
                ]
            });
        }

        return this.orderInfo;
    },

    getCustomerInfo : function() {
        if(!this.customerInfo) {
            this.customerInfo = Ext.create('Ext.panel.Panel', {
                title : t('coreshop_customer') + ": " + this.order.customer.firstname + " (" + this.order.customer.o_id + ")",
                margin : '0 0 20 0',
                border : true,
                flex : 6,
                iconCls : 'coreshop_icon_customer',
                tools : [
                    {
                        type: 'coreshop-open',
                        tooltip: t('open'),
                        handler : function() {
                            pimcore.helpers.openObject(this.order.customer.o_id);
                        }.bind(this)
                    }
                ],
                items : [
                    {
                        xtype : 'tabpanel',
                        items: [
                            this.getAddressPanelForAddress(this.order.address.shipping, t("coreshop_address_shipping")),
                            this.getAddressPanelForAddress(this.order.address.billing, t("coreshop_address_billing"))
                        ]
                    }
                ]
            });
        }

        return this.customerInfo;
    },

    getAddressPanelForAddress : function(address, title) {
        return {
            xtype: 'panel',
            title: title,
            layout: {
                type : 'hbox',
                align : 'stretch'
            },
            height : 220,
            items: [
                {
                    xtype: 'panel',
                    bodyPadding : 5,
                    html :
                        address.firstname + " " + address.lastname + "<br/>" +
                        address.street + " " + address.nr + "<br/>" +
                        address.zip + " " + address.city + "<br/>" +
                        address.country.name,
                    flex : 1
                },
                {
                    xtype: 'panel',
                    html : '<img src="https://maps.googleapis.com/maps/api/staticmap?zoom=13&size=200x200&maptype=roadmap'
                                +"&center=" + address.street + "+" + address.nr + "+" + address.zip + "+" + address.city + "+" + address.country.name
                                +"&markers=color:blue|" + address.street + "+" + address.nr + "+" + address.zip + "+" + address.city + "+" + address.country.name
                            +'" />',
                    flex : 1,
                    bodyPadding : 5,
                },
            ]
        };
    },

    getShippingInfo : function() {
        if(!this.shippingInfo) {
            var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
                listeners : {
                    edit : function(editor, context, eOpts) {
                        var trackingCode = context.record.get("tracking");

                        Ext.Ajax.request({
                            url: "/plugin/CoreShop/admin_order/change-tracking-code",
                            params: {
                                id: this.order.o_id,
                                trackingCode : trackingCode
                            },
                            success: function (response) {
                                context.record.commit();

                            }.bind(this)
                        });
                    }.bind(this)
                }
            });

            this.shippingInfo = Ext.create('Ext.panel.Panel', {
                title : t('coreshop_carrier'),
                border : true,
                margin : '0 20 20 0',
                iconCls : 'coreshop_icon_carriers',
                items : [
                    {
                        xtype : 'grid',
                        margin: '0 0 15 0',
                        cls : 'coreshop-order-detail-grid',
                        store :  new Ext.data.JsonStore({
                            data : [this.order.shipping]
                        }),
                        plugins: [
                            cellEditing
                        ],
                        columns : [
                            {
                                xtype : 'gridcolumn',
                                dataIndex : 'carrier',
                                text : t('coreshop_carrier'),
                                flex : 1
                            },
                            {
                                xtype : 'gridcolumn',
                                flex : 1,
                                dataIndex : 'weight',
                                text : t('coreshop_carrier_shippingMethod_weight')
                            },
                            {
                                xtype : 'gridcolumn',
                                dataIndex : 'cost',
                                text : t('coreshop_shipping_cost'),
                                flex : 1,
                                align : 'right',
                                renderer: coreshop.util.format.currency.bind(this, this.order.currency.symbol)
                            },
                            {
                                xtype : 'gridcolumn',
                                dataIndex : 'tracking',
                                text : t('coreshop_carrier_tracking_code'),
                                flex : 1,
                                field: {
                                    xtype: 'textfield'
                                }
                            },
                            {
                                xtype : 'gridcolumn',
                                dataIndex : 'carrier',
                                width : 100,
                                align : 'right',
                                renderer : function(value, metaData, record, rowIndex, colIndex) {
                                    var id = Ext.id();
                                    Ext.defer(function () {
                                        Ext.widget('button', {
                                            renderTo: id,
                                            text: t('edit'),
                                            width : '100%',
                                            handler: function () {
                                                cellEditing.startEditByPosition({
                                                    row: rowIndex,
                                                    column : colIndex - 1
                                                });
                                            }.bind(this)
                                        });
                                    }, 50);
                                    return Ext.String.format('<div id="{0}"></div>', id);
                                }
                            }
                        ]
                    }
                ]
            });
        }

        return this.shippingInfo;
    },

    getPaymentInfo : function() {
        if(!this.paymentInfo) {
            this.paymentsStore = new Ext.data.JsonStore({
                data : this.order.payments
            });

            this.paymentInfo = Ext.create('Ext.panel.Panel', {
                title : t('coreshop_payments'),
                border : true,
                margin : '0 20 20 0',
                iconCls : 'coreshop_icon_payment',
                tools : [
                    {
                        type: 'coreshop-add',
                        tooltip: t('add'),
                        handler : function() {
                            pimcore.plugin.coreshop.orders.createPayment.showWindow(this.order.o_id, this.order, function(result) {
                                if(result.success) {
                                    this.paymentsStore.loadData(result.payments);
                                }
                            }.bind(this));
                        }.bind(this)
                    }
                ],
                items : [
                    {
                        xtype : 'grid',
                        margin: '0 0 15 0',
                        cls : 'coreshop-order-detail-grid',
                        store :  this.paymentsStore,
                        columns : [
                            {
                                xtype : 'gridcolumn',
                                dataIndex : 'datePayment',
                                text : t('date'),
                                flex : 1,
                                renderer : function(val) {
                                    if(val) {
                                        return Ext.Date.format(new Date(val * 1000), 'Y-m-d H:i:s')
                                    }

                                    return '';
                                }
                            },
                            {
                                xtype : 'gridcolumn',
                                flex : 1,
                                dataIndex : 'provider',
                                text : t('coreshop_paymentProvider')
                            },
                            {
                                xtype : 'gridcolumn',
                                dataIndex : 'transactionIdentifier',
                                text : t('coreshop_transactionNumber'),
                                flex : 1,
                                align : 'right'
                            },
                            {
                                xtype : 'gridcolumn',
                                dataIndex : 'amount',
                                text : t('coreshop_amount'),
                                flex : 1,
                                renderer: coreshop.util.format.currency.bind(this, this.order.currency.symbol)
                            }
                        ]
                    }
                ]

            });
        }

        return this.paymentInfo;
    },

    getMessagesInfo : function() {
        if(!this.messagesInfo) {
            this.messagesInfo = Ext.create('Ext.panel.Panel', {
                title : t('coreshop_messaging_messages'),
                border : true,
                margin : '0 0 20 0',
                iconCls : 'coreshop_icon_messaging'
            });
        }

        return this.messagesInfo;
    },

    getPluginInfo : function() {
        var pluginInfo = coreshop.plugin.broker.fireEvent(this.order.paymentProvider + '.order', this);

        if(pluginInfo) {
            return pluginInfo
        }

        return null;
    },

    getDetailInfo : function() {
        if(!this.detailsInfo) {
            this.detailsStore = new Ext.data.JsonStore({
                data : this.order.details
            });

            this.summaryStore = new Ext.data.JsonStore({
                data : this.order.summary
            });

            this.detailsInfo = Ext.create('Ext.panel.Panel', {
                title : t('coreshop_products'),
                border : true,
                margin : '0 0 20 0',
                iconCls : 'coreshop_icon_product',
                items : [
                    {
                        xtype : 'grid',
                        margin: '0 0 15 0',
                        cls : 'coreshop-order-detail-grid',
                        store :  this.detailsStore,
                        columns : [
                            {
                                xtype : 'gridcolumn',
                                flex : 1,
                                dataIndex : 'product_name',
                                text : t('coreshop_product')
                            },
                            {
                                xtype : 'gridcolumn',
                                dataIndex : 'price_without_tax',
                                text : t('coreshop_price_without_tax'),
                                width : 150,
                                align : 'right',
                                renderer: coreshop.util.format.currency.bind(this, this.order.currency.symbol)
                            },
                            {
                                xtype : 'gridcolumn',
                                dataIndex : 'price',
                                text : t('coreshop_price_with_tax'),
                                width : 150,
                                align : 'right',
                                renderer: coreshop.util.format.currency.bind(this, this.order.currency.symbol)
                            },
                            {
                                xtype : 'gridcolumn',
                                dataIndex : 'amount',
                                text : t('coreshop_amount'),
                                width : 150,
                                align : 'right'
                            },
                            {
                                xtype : 'gridcolumn',
                                dataIndex : 'total_tax',
                                text : t('coreshop_total_tax'),
                                width : 150,
                                align : 'right',
                                renderer: coreshop.util.format.currency.bind(this, this.order.currency.symbol)
                            },
                            {
                                xtype : 'gridcolumn',
                                dataIndex : 'total',
                                text : t('coreshop_total'),
                                width : 150,
                                align : 'right',
                                renderer: coreshop.util.format.currency.bind(this, this.order.currency.symbol)
                            },
                            /*{
                                xtype : 'gridcolumn',
                                dataIndex : 'productName',
                                width : 100,
                                align : 'right',
                                renderer : function(value, metaData, record, rowIndex, colIndex) {
                                    var id = Ext.id();
                                    Ext.defer(function () {
                                        Ext.widget('button', {
                                            renderTo: id,
                                            text: t('edit'),
                                            width : '100%',
                                            handler: function () {

                                            }.bind(this)
                                        });
                                    }, 50);
                                    return Ext.String.format('<div id="{0}"></div>', id);
                                }
                            }*/
                        ]
                    },
                    {
                        xtype : 'grid',
                        margin: '0 0 15 0',
                        cls : 'coreshop-order-detail-grid',
                        store :  this.summaryStore,
                        hideHeaders : true,
                        columns : [
                            {
                                xtype : 'gridcolumn',
                                flex : 1,
                                align: 'right',
                                dataIndex : 'key',
                                renderer : function(value, metaData, record) {
                                    return '<span style="font-weight:bold">' + t('coreshop_' + value) + '</span>';
                                }
                            },
                            {
                                xtype : 'gridcolumn',
                                dataIndex : 'value',
                                width : 150,
                                align : 'right',
                                renderer : function(value, metaData, record) {
                                    return '<span style="font-weight:bold">' + coreshop.util.format.currency(this.order.currency.symbol, value) + '</span>';
                                }.bind(this)
                            }
                        ]
                    }
                ]
            });
        }

        return this.detailsInfo;
    }
});