/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
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
        this.layoutId = 'coreshop_order_' + this.order.o_id;

        this.getLayout();
    },

    activate: function () {
        var tabPanel = Ext.getCmp('pimcore_panel_tabs');
        tabPanel.setActiveItem(this.layoutId);
    },

    reload : function () {
        this.layout.destroy();

        coreshop.helpers.openOrder(this.order.o_id);
    },

    getLayout: function () {
        if (!this.layout) {

            // create new panel
            this.layout = new Ext.panel.Panel({
                id: this.layoutId,
                title: t('coreshop_order') + ': ' + this.order.orderNumber,
                iconCls: 'coreshop_icon_orders',
                border: false,
                layout: 'border',
                autoScroll: true,
                closable: true,
                items: this.getItems(),
                dockedItems: [{
                    xtype: 'toolbar',
                    dock: 'top',
                    items: [
                        {
                            iconCls : 'pimcore_icon_reload',
                            text : t('reload'),
                            handler : function () {
                                this.reload();
                            }.bind(this)
                        }
                    ]
                }]
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

        var visitorInfo = this.getVisitorInfo();

        if(visitorInfo) {
            leftItems.push(visitorInfo);
        }

        var rightItems = [
            this.getCustomerInfo(),
            this.getMessagesInfo(),
        ];

        var contentItems = [
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
                items : contentItems
            }
        ];

        var paymentPluginPanel = this.getPaymentPluginInfo();
        var pluginPanel = this.getPluginInfo();

        if (pluginPanel) {
            items.push(pluginPanel);
        }

        if (paymentPluginPanel) {
            items.push(paymentPluginPanel);
        }

        items.push(this.getDetailInfo());

        this.panel = Ext.create('Ext.container.Container', {
            border : false,
            items : items,
            padding : 20,
            region : 'center',
            defaults : defaults
        });

        return this.panel;
    },

    getHeader : function () {
        if (!this.headerPanel) {
            var items = [
                {
                    xtype : 'panel',
                    html : t('coreshop_date') + '<br/><span class="coreshop_order_big">' + Ext.Date.format(new Date(this.order.orderDate * 1000), t('coreshop_date_time_format')) + '</span>',
                    bodyPadding : 20,
                    flex : 1
                },
                {
                    xtype : 'panel',
                    html : t('coreshop_orders_total') + '<br/><span class="coreshop_order_big">' + coreshop.util.format.currency(this.order.currency.symbol, this.order.total) + '</span>',
                    bodyPadding : 20,
                    flex : 1
                },
                {
                    xtype : 'panel',
                    html : t('coreshop_messaging_messages') + '<br/><span class="coreshop_order_big">' + 0 + '</span>', //TODO: Add Messages
                    bodyPadding : 20,
                    flex : 1
                },
                {
                    xtype : 'panel',
                    html : t('coreshop_product_count') + '<br/><span class="coreshop_order_big">' + this.order.items.length + '</span>',
                    bodyPadding : 20,
                    flex : 1
                }
            ];

            if (coreshop.settings.multishop) {
                items.push({
                    xtype : 'panel',
                    html : t('coreshop_shop') + '<br/><span class="coreshop_order_big">' + this.order.shop.name + '</span>',
                    bodyPadding : 20,
                    flex : 1
                });
            }

            this.headerPanel = Ext.create('Ext.panel.Panel', {
                layout : 'hbox',
                margin : '0 0 20 0',
                items : items
            });
        }

        return this.headerPanel;
    },

    getOrderInfo : function () {
        if (!this.orderInfo) {
            this.orderStatesStore = new Ext.data.JsonStore({
                data : this.order.statesHistory
            });

            this.orderInfo = Ext.create('Ext.panel.Panel', {
                title : t('coreshop_order') + ': ' + this.order.orderNumber + ' (' + this.order.o_id + ')',
                margin : '0 20 20 0',
                border : true,
                flex : 8,
                iconCls : 'coreshop_icon_orders',
                tools : [
                    {
                        type: 'coreshop-open',
                        tooltip: t('open'),
                        handler : function () {
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
                            handler : function () {
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
                                renderer : function (value, metaData) {
                                    var store = pimcore.globalmanager.get('coreshop_orderstates');
                                    var orderState = store.getById(value);

                                    if (orderState) {
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
                                renderer : function (value) {
                                    var store = pimcore.globalmanager.get('coreshop_orderstates');
                                    var orderState = store.getById(value);

                                    if (orderState && orderState.get('email') === '1') {
                                        var id = Ext.id();
                                        Ext.defer(function () {
                                            Ext.widget('button', {
                                                renderTo: id,
                                                text: t('coreshop_order_resend_email'),
                                                flex : 1,
                                                handler: function () {
                                                    Ext.Ajax.request({
                                                        url: '/plugin/CoreShop/admin_order/resend-order-state-mail',
                                                        params: {
                                                            id: this.order.o_id,
                                                            orderStateId : orderState.get('id')
                                                        },
                                                        success: function (response) {
                                                            var res = Ext.decode(response.responseText);

                                                            if (res.success) {
                                                                pimcore.helpers.showNotification(t('success'), t('success'), 'success');
                                                            } else {
                                                                pimcore.helpers.showNotification(t('error'), t(res.message), 'error');
                                                            }
                                                        }.bind(this)
                                                    });
                                                }.bind(this)
                                            });
                                        }.bind(this), 50);
                                        return Ext.String.format('<div id="{0}"></div>', id);
                                    }

                                    return '';
                                }.bind(this)
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
                                store: pimcore.globalmanager.get('coreshop_orderstates'),
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
                                handler : function (button) {
                                    var comboBox = button.previousSibling();

                                    Ext.Ajax.request({
                                        url: '/plugin/CoreShop/admin_order/change-order-state',
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

    getCustomerInfo : function () {
        if (!this.customerInfo) {
            var items = [

            ];

            if(this.order.customer) {
                if (this.order.customer.isGuest) {
                    items.push({
                        xtype: 'label',
                        text: t('coreshop_order_is_guest')
                    });
                } else {
                    items.push({
                        xtype: 'panel',
                        bodyPadding: 10,
                        margin: '0 0 10px 0',
                        style: this.borderStyle,
                        items: [
                            {
                                xtype: 'label',
                                style: 'font-weight:bold;display:block',
                                text: t('email')
                            },
                            {
                                xtype: 'label',
                                style: 'display:block',
                                text: this.order.customer.email
                            },
                            {
                                xtype: 'label',
                                style: 'font-weight:bold;display:block',
                                text: t('coreshop_customer_created')
                            },
                            {
                                xtype: 'label',
                                style: 'display:block',
                                text: Ext.Date.format(new Date(this.order.customer.o_creationDate * 1000), t('coreshop_date_time_format'))
                            }
                        ]
                    });
                }
            }

            items.push({
                xtype: 'tabpanel',
                items: [
                    this.getAddressPanelForAddress(this.order.address.shipping, t('coreshop_address_shipping'), 'shipping'),
                    this.getAddressPanelForAddress(this.order.address.billing, t('coreshop_address_billing'), 'billing')
                ]
            });

            this.customerInfo = Ext.create('Ext.panel.Panel', {
                title : t('coreshop_customer') + ': ' + (this.order.customer ? this.order.customer.firstname + ' (' + this.order.customer.o_id + ')' : t('unknown')),
                margin : '0 0 20 0',
                border : true,
                flex : 6,
                iconCls : 'coreshop_icon_customer',
                tools : [
                    {
                        type: 'coreshop-open',
                        tooltip: t('open'),
                        handler : function () {
                            if(this.order.customer) {
                                pimcore.helpers.openObject(this.order.customer.o_id);
                            }
                        }.bind(this)
                    }
                ],
                items : items
            });
        }

        return this.customerInfo;
    },

    getAddressPanelForAddress : function (address, title, type) {
        var panel = {
            xtype: 'panel',
            dockedItems: [{
                xtype: 'toolbar',
                dock: 'top',
                items: [
                    {
                        iconCls : 'pimcore_icon_edit',
                        text : t('edit'),
                        scale : 'small',
                        handler : function () {
                            Ext.Ajax.request({
                                url: '/plugin/CoreShop/admin_order/get-address-fields',
                                params: {
                                    id: this.order.o_id,
                                    type : type
                                },
                                success: function (response) {
                                    var res = Ext.decode(response.responseText);

                                    var addressWindow = new pimcore.plugin.coreshop.orders.address(res.data, res.layout, this.order.o_id, type, title, function (success) {
                                        addressWindow.close();
                                        if (success) {
                                            this.reload();
                                        }
                                    }.bind(this)).show();
                                }.bind(this)
                            });
                        }.bind(this)
                    }, '->',
                    {
                        iconCls: 'coreshop_icon_open',
                        text: t('open'),
                        handler : function () {
                            pimcore.helpers.openObject(address.o_id);
                        }.bind(this)
                    }
                ]
            }],
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
                    (address.firstname ? address.firstname : '') + ' ' + (address.lastname ? address.lastname  : '') + '<br/>' +
                    (address.company ? address.company + '<br/>' : '') +
                    (address.street ? address.street  : '') + ' ' + (address.nr ? address.nr : '') + '<br/>' +
                    (address.zip ? address.zip : '') + ' ' + (address.city ? address.city : '') + '<br/>' +
                    address.country.name,
                    flex : 1
                }
            ]
        };

        if(pimcore.settings.google_maps_api_key) {
            panel.items.push({
                xtype: 'panel',
                html : '<img src="https://maps.googleapis.com/maps/api/staticmap?zoom=13&size=200x200&maptype=roadmap'
                + '&center=' + address.street + '+' + address.nr + '+' + address.zip + '+' + address.city + '+' + address.country.name
                + '&markers=color:blue|' + address.street + '+' + address.nr + '+' + address.zip + '+' + address.city + '+' + address.country.name
                + '&key=' + pimcore.settings.google_maps_api_key
                + '" />',
                flex : 1,
                bodyPadding : 5,
            });
        }

        return panel;
    },

    getShippingInfo : function () {
        if (!this.shippingInfo) {
            var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
                listeners : {
                    edit : function (editor, context, eOpts) {
                        var trackingCode = context.record.get('tracking');

                        Ext.Ajax.request({
                            url: '/plugin/CoreShop/admin_order/change-tracking-code',
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
                                menuDisabled: true,
                                sortable: false,
                                xtype: 'actioncolumn',
                                width: 50,
                                items: [{
                                    iconCls: 'pimcore_icon_edit',
                                    tooltip: t('Edit'),
                                    handler: function (grid, rowIndex, colIndex) {
                                        cellEditing.startEditByPosition({
                                            row: rowIndex,
                                            column : colIndex - 1
                                        });
                                    }.bind(this)
                                }]
                            }
                        ]
                    }
                ]
            });
        }

        return this.shippingInfo;
    },

    updatePaymentInfoAlert : function () {
        if (this.paymentInfoAlert) {
            if (this.order.totalPayed < this.order.total || this.order.totalPayed > this.order.total) {
                this.paymentInfoAlert.update(t('coreshop_order_payment_paid_warning').format(coreshop.util.format.currency(this.order.currency.symbol, this.order.totalPayed), coreshop.util.format.currency(this.order.currency.symbol, this.order.total)));
                this.paymentInfoAlert.show();
            } else {
                this.paymentInfoAlert.update('');
                this.paymentInfoAlert.hide();
            }
        }
    },

    getPaymentInfo : function () {
        if (!this.paymentInfo) {
            this.paymentsStore = new Ext.data.JsonStore({
                data : this.order.payments
            });

            this.paymentInfoAlert = Ext.create('Ext.panel.Panel', {
                xtype : 'panel',
                cls : 'x-coreshop-alert',
                bodyPadding : 5,
                hidden : true
            });
            this.updatePaymentInfoAlert();

            var items = [
                this.paymentInfoAlert
            ];

            items.push({
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
                        renderer : function (val) {
                            if (val) {
                                return Ext.Date.format(new Date(val * 1000), t('coreshop_date_time_format'));
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
                        handler : function () {
                            pimcore.plugin.coreshop.orders.createPayment.showWindow(this.order.o_id, this.order, function (result) {
                                if (result.success) {
                                    this.paymentsStore.loadData(result.payments);
                                    this.order.totalPayed = result.totalPayed;

                                    this.updatePaymentInfoAlert();
                                }
                            }.bind(this));
                        }.bind(this)
                    }
                ],
                items : items
            });
        }

        return this.paymentInfo;
    },

    getMessagesInfo : function () {
        if (!this.messagesInfo) {
            this.messagesInfo = Ext.create('Ext.panel.Panel', {
                title : t('coreshop_messaging_messages'),
                border : true,
                margin : '0 0 20 0',
                iconCls : 'coreshop_icon_messaging',
                items : [
                    {
                        xtype : 'form',
                        bodyStyle:'padding:20px 5px 20px 5px;',
                        border: false,
                        autoScroll: true,
                        forceLayout: true,
                        fieldDefaults: {
                            labelWidth: 150
                        },
                        buttons: [
                            {
                                text: t('coreshop_message_send'),
                                handler: function (btn) {
                                    var formObj = btn.up('form');
                                    var form = formObj.getForm();

                                    if (form.isValid()) {
                                        var formValues = form.getFieldValues();

                                        formValues['o_id'] = this.order.o_id;

                                        Ext.Ajax.request({
                                            url : '/plugin/CoreShop/admin_order/send-message',
                                            method : 'post',
                                            params : formValues,
                                            callback: function (request, success, response) {
                                                try {
                                                    response = Ext.decode(response.responseText);

                                                    if (response.success) {
                                                        formObj.down('textarea').setValue('');

                                                        pimcore.helpers.showNotification(t('success'), t('coreshop_message_send_success'), 'success');
                                                    } else {
                                                        Ext.Msg.alert(t('error'), response.message);
                                                    }
                                                }
                                                catch (e) {
                                                    Ext.Msg.alert(t('error'), e);
                                                }
                                            }
                                        });
                                    }
                                }.bind(this),
                                iconCls: 'pimcore_icon_apply'
                            }
                        ],
                        items : [
                            {
                                xtype: 'textarea',
                                name: 'message',
                                style: "font-family: 'Courier New', Courier, monospace;",
                                width : '100%',
                                height : '100%'
                            }
                        ]
                    }
                ]
            });
        }

        return this.messagesInfo;
    },

    getPluginInfo : function () {
        var pluginInfo = coreshop.plugin.broker.fireEvent('orderDetail', this);

        if (pluginInfo.length > 0) {

            return {
                xtype: 'container',
                layout: 'hbox',
                margin: '0 0 20 0',
                border: 0,
                style: {
                    border: 0
                },
                items: pluginInfo
            };
        }

        return null;
    },

    getPaymentPluginInfo : function () {
        var pluginInfo = coreshop.plugin.broker.fireEvent('orderDetailPayment' + this.order.paymentProvider.ucfirst(), this);

        if (pluginInfo) {
            return {
                xtype: 'container',
                layout: 'hbox',
                margin: '0 0 20 0',
                border: 0,
                style: {
                    border: 0
                },
                items: pluginInfo
            };
        }

        return null;
    },

    getVisitorInfo : function() {
        if(this.order.visitor) {
            if (!this.visitorInfo) {
                var visitor = this.order.visitor;

                this.visitorInfo = Ext.create('Ext.panel.Panel', {
                    title : t('coreshop_visitor'),
                    border : true,
                    margin : '0 20 20 0',
                    iconCls : 'coreshop_icon_visitor',
                    items : [
                        {
                            xtype: 'panel',
                            bodyPadding : 5,
                            html :
                            ('<strong>' + t('coreshop_visitor_ip') + '</strong>: ' + coreshop.helpers.long2ip(visitor.ip))  + '<br/>' +
                            ('<strong>' + t('coreshop_visitor_referrer') + '</strong>: ' + visitor.referrer) + '<br/>' +
                            ('<strong>' + t('coreshop_visitor_date')+ '</strong>: ' + Ext.Date.format(new Date(visitor.creationDate * 1000), t('coreshop_date_time_format'))) + '<br/>',
                            flex : 1
                        }
                    ]
                });
            }

            return this.visitorInfo;
        }

        return false;
    },

    getDetailInfo : function () {
        if (!this.detailsInfo) {
            this.detailsStore = new Ext.data.JsonStore({
                data : this.order.details
            });

            this.summaryStore = new Ext.data.JsonStore({
                data : this.order.summary
            });

            var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
                listeners : {
                    edit : function (editor, context, eOpts) {
                        if (context.originalValue != context.value) {
                            Ext.Ajax.request({
                                url: '/plugin/CoreShop/admin_order/change-order-item',
                                params: {
                                    id: this.order.o_id,
                                    orderItemId : context.record.get('o_id'),
                                    amount : context.record.get('amount'),
                                    price : context.record.get('price_without_tax')
                                },
                                success: function (response) {
                                    var res = Ext.decode(response.responseText);

                                    if (res.success) {
                                        context.record.commit();

                                        //this.reload();

                                        this.detailsStore.loadData(res.details);
                                        this.summaryStore.loadData(res.summary);

                                        this.order.total = res.total;

                                        this.updatePaymentInfoAlert();
                                    } else {
                                        pimcore.helpers.showNotification(t('error'), t('coreshop_save_error'), 'error');
                                    }

                                }.bind(this)
                            });
                        }
                    }.bind(this)
                }
            });

            var itemsGrid = {
                xtype : 'grid',
                margin: '0 0 15 0',
                cls : 'coreshop-order-detail-grid',
                store :  this.detailsStore,
                plugins: [
                    cellEditing
                ],
                columns : [
                    {
                        xtype : 'gridcolumn',
                        flex : 1,
                        dataIndex : 'product_name',
                        text : t('coreshop_product')
                    },
                    {
                        xtype : 'gridcolumn',
                        dataIndex : 'wholesale_price',
                        text : t('coreshop_wholesale_price'),
                        width : 150,
                        align : 'right',
                        renderer: coreshop.util.format.currency.bind(this, this.order.currency.symbol)
                    },
                    {
                        xtype : 'gridcolumn',
                        dataIndex : 'price_without_tax',
                        text : t('coreshop_price_without_tax'),
                        width : 150,
                        align : 'right',
                        renderer: coreshop.util.format.currency.bind(this, this.order.currency.symbol),
                        field : {
                            xtype: 'numberfield',
                            decimalPrecision : 4
                        }
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
                        align : 'right',
                        field : {
                            xtype: 'numberfield',
                            decimalPrecision : 0
                        }
                    },
                    {
                        xtype : 'gridcolumn',
                        dataIndex : 'total',
                        text : t('coreshop_total'),
                        width : 150,
                        align : 'right',
                        renderer: coreshop.util.format.currency.bind(this, this.order.currency.symbol)
                    },
                    {
                        menuDisabled: true,
                        sortable: false,
                        xtype: 'actioncolumn',
                        width: 50,
                        items: [{
                            iconCls: 'pimcore_icon_edit',
                            tooltip: t('edit'),
                            handler: function (grid, rowIndex, colIndex) {
                                cellEditing.startEditByPosition({
                                    row: rowIndex,
                                    column : 4
                                });
                            }.bind(this)
                        }, {
                            iconCls: 'pimcore_icon_open',
                            tooltip : t('open'),
                            handler : function (grid, rowIndex) {
                                var record = grid.getStore().getAt(rowIndex);

                                pimcore.helpers.openObject(record.get('o_id'));
                            }
                        }]
                    }
                ]
            };

            var summaryGrid = {
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
                        renderer : function (value, metaData, record) {
                            if(record.get("text")) {
                                return '<span style="font-weight:bold">' + record.get("text") + '</span>';
                            }

                            return '<span style="font-weight:bold">' + t('coreshop_' + value) + '</span>';
                        }
                    },
                    {
                        xtype : 'gridcolumn',
                        dataIndex : 'value',
                        width : 150,
                        align : 'right',
                        renderer : function (value, metaData, record) {
                            return '<span style="font-weight:bold">' + coreshop.util.format.currency(this.order.currency.symbol, value) + '</span>';
                        }.bind(this)
                    }
                ]
            };

            var detailItems = [itemsGrid, summaryGrid];

            if (this.order.priceRule) {

                var priceRuleStore = new Ext.data.JsonStore({
                    data : this.order.priceRule
                });

                var priceRuleItem =  {
                    xtype : 'grid',
                    margin: '0 0 15 0',
                    cls : 'coreshop-order-detail-grid',
                    store :  priceRuleStore,
                    hideHeaders : true,
                    title : t('coreshop_pricerules'),
                    columns : [
                        {
                            xtype : 'gridcolumn',
                            flex : 1,
                            align: 'right',
                            dataIndex : 'name'
                        },
                        {
                            xtype : 'gridcolumn',
                            dataIndex : 'discount',
                            width : 150,
                            align : 'right',
                            renderer : function (value, metaData, record) {
                                return '<span style="font-weight:bold">' + coreshop.util.format.currency(this.order.currency.symbol, value) + '</span>';
                            }.bind(this)
                        }
                    ]
                };

                detailItems.splice(1, 0, priceRuleItem);
            }

            this.detailsInfo = Ext.create('Ext.panel.Panel', {
                title : t('coreshop_products'),
                border : true,
                margin : '0 0 20 0',
                iconCls : 'coreshop_icon_product',
                items : detailItems
            });
        }

        return this.detailsInfo;
    }
});
