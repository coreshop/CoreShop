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

pimcore.registerNS('coreshop.order.order.detail');
coreshop.order.order.detail = Class.create(coreshop.order.sale.detail, {
    type: 'order',

    getTopButtons: function () {
        var buttons = [];

        if (this.sale.invoiceCreationAllowed) {
            buttons.push({
                iconCls: 'coreshop_icon_orders_invoice',
                text: t('coreshop_invoice_create_short'),
                handler: function () {
                    this.createInvoice();
                }.bind(this)
            });
        }

        if (this.sale.shipmentCreationAllowed) {
            buttons.push({
                iconCls: 'coreshop_icon_orders_shipment',
                text: t('coreshop_shipment_create_short'),
                handler: function () {
                    this.createShipment();
                }.bind(this)
            });
        }

        return buttons;
    },

    getLeftItems: function () {
        return [
            this.getSaleInfo(),
            this.getPaymentDetails(),
            this.getShipmentDetails(),
            this.getInvoiceDetails(),
            this.getMailDetails()
        ];
    },

    getHeader: function () {
        if (!this.headerPanel) {

            var items1 = [
                {
                    xtype: 'panel',
                    html: t('coreshop_workflow_name_coreshop_order') + '<br/><span class="coreshop_order_big order_state"><span class="color-dot" style="background-color:' + this.sale.orderState.color + ';"></span> ' + this.sale.orderState.label + '</span>',
                    bodyPadding: '10 20',
                    flex: 1
                },
                {
                    xtype: 'panel',
                    html: t('coreshop_workflow_name_coreshop_order_payment') + '<br/><span class="coreshop_order_medium"><span class="color-dot" style="background-color:' + this.sale.orderPaymentState.color + ';"></span>' + this.sale.orderPaymentState.label + '</span>',
                    bodyPadding: '10 20',
                    flex: 1
                },
                {
                    xtype: 'panel',
                    html: t('coreshop_workflow_name_coreshop_order_shipment') + '<br/><span class="coreshop_order_medium"><span class="color-dot" style="background-color:' + this.sale.orderShippingState.color + ';"></span>' + this.sale.orderShippingState.label + '</span>',
                    bodyPadding: '10 20',
                    flex: 1
                },
                {
                    xtype: 'panel',
                    html: t('coreshop_workflow_name_coreshop_order_invoice') + '<br/><span class="coreshop_order_medium"><span class="color-dot" style="background-color:' + this.sale.orderInvoiceState.color + ';"></span>' + this.sale.orderInvoiceState.label + '</span>',
                    bodyPadding: '10 20',
                    flex: 1
                }
            ];

            var items2 = [
                {
                    xtype: 'panel',
                    html: t('coreshop_date') + '<br/><span class="coreshop_order_big">' + Ext.Date.format(new Date(this.sale.saleDate * 1000), t('coreshop_date_time_format')) + '</span>',
                    bodyPadding: 20,
                    flex: 1
                },
                {
                    xtype: 'panel',
                    html: t('coreshop_sale_total') + '<br/><span class="coreshop_order_big">' + coreshop.util.format.currency(this.sale.currency.symbol, this.sale.totalGross) + '</span>',
                    bodyPadding: 20,
                    flex: 1
                },
                {
                    xtype: 'panel',
                    html: t('coreshop_product_count') + '<br/><span class="coreshop_order_big">' + this.sale.items.length + '</span>',
                    bodyPadding: 20,
                    flex: 1
                },
                {
                    xtype: 'panel',
                    html: t('coreshop_store') + '<br/><span class="coreshop_order_big">' + this.sale.store.name + '</span>',
                    bodyPadding: 20,
                    flex: 1
                }
            ];

            var statusPanel1 = Ext.create('Ext.panel.Panel', {
                layout: 'hbox',
                margin: 0,
                items: items1
            });

            var statusPanel2 = Ext.create('Ext.panel.Panel', {
                layout: 'hbox',
                margin: 0,
                items: items2
            });

            this.headerPanel = Ext.create('Ext.panel.Panel', {
                border: false,
                margin: '0 0 20 0',
                items: [statusPanel1, statusPanel2]
            });
        }

        return this.headerPanel;
    },

    getSaleInfo: function ($super) {
        if (!this.saleInfo) {
            var orderInfo = $super();

            this.saleStatesStore = new Ext.data.JsonStore({
                data: this.sale.statesHistory
            });

            if (this.sale.availableOrderTransitions.length > 0) {
                var buttons = [],
                    changeStateRequest = function (context, btn, transitionInfo) {
                        btn.disable();
                        Ext.Ajax.request({
                            url: '/admin/coreshop/order/update-order-state',
                            params: {
                                transition: transitionInfo.transition,
                                o_id: context.sale.o_id
                            },
                            success: function (response) {
                                var res = Ext.decode(response.responseText);
                                if(res.success === true) {
                                    context.reload();
                                } else {
                                    Ext.Msg.alert(t('error'), res.message);
                                    btn.enable();
                                }
                            },
                            failure: function () {
                                btn.enable();
                            }
                        });
                    };

                Ext.Array.each(this.sale.availableOrderTransitions, function (transitionInfo) {
                    buttons.push({
                        xtype: 'button',
                        style: transitionInfo.transition === 'cancel' ? '' : 'background-color:#524646;border-left:10px solid ' + transitionInfo.color + ' !important;',
                        cls: transitionInfo.transition === 'cancel' ? 'coreshop_change_order_order_state_button coreshop_cancel_order_button' : 'coreshop_change_order_order_state_button',
                        text: transitionInfo.label,
                        handler: function (btn) {
                            if (transitionInfo.transition === 'cancel') {
                                Ext.MessageBox.confirm(t('info'), t('coreshop_cancel_order_confirm'), function (buttonValue) {
                                    if (buttonValue === 'yes') {
                                        changeStateRequest(this, btn, transitionInfo);
                                    }
                                }.bind(this));
                            } else {
                                changeStateRequest(this, btn, transitionInfo);
                            }
                        }.bind(this)
                    })
                }.bind(this));

                orderInfo.add({
                    xtype: 'panel',
                    layout: 'hbox',
                    margin: 0,
                    items: buttons
                });
            }

            orderInfo.add({
                xtype: 'grid',
                margin: '0 0 15 0',
                cls: 'coreshop-detail-grid',
                store: this.saleStatesStore,
                columns: [
                    {
                        xtype: 'gridcolumn',
                        flex: 1,
                        dataIndex: 'title',
                        text: t('coreshop_orderstate')
                    },
                    {
                        xtype: 'gridcolumn',
                        width: 250,
                        dataIndex: 'date',
                        text: t('date')
                    }
                ]
            });
        }

        return this.saleInfo;
    },

    getRightItems: function ($super) {
        var rightItems = $super();
        rightItems.push(this.getCommentDetails());
        return rightItems;
    },

    getShipmentDetails: function () {
        if (!this.shippingInfo) {

            this.shipmentsStore = new Ext.data.JsonStore({
                data: this.sale.shipments
            });

            this.shippingInfo = Ext.create('Ext.panel.Panel', {
                title: t('coreshop_shipments'),
                border: true,
                margin: '0 20 20 0',
                iconCls: 'coreshop_icon_orders_shipment',
                items: [
                    {
                        xtype: 'grid',
                        margin: '0 0 15 0',
                        cls: 'coreshop-detail-grid',
                        store: this.shipmentsStore,
                        columns: [
                            {
                                xtype: 'gridcolumn',
                                flex: 1,
                                dataIndex: 'shipmentDate',
                                text: t('coreshop_date'),
                                renderer: function (val) {
                                    if (val) {
                                        return Ext.Date.format(new Date(val * 1000), t('coreshop_date_time_format'));
                                    }
                                    return '';
                                }
                            },
                            {
                                xtype: 'gridcolumn',
                                flex: 1,
                                dataIndex: 'carrierName',
                                text: t('coreshop_carrier')
                            },
                            {
                                xtype: 'gridcolumn',
                                dataIndex: 'trackingCode',
                                text: t('coreshop_tracking_code'),
                                flex: 1,
                                field: {
                                    xtype: 'textfield'
                                }
                            },
                            {
                                xtype: 'widgetcolumn',
                                flex: 1,
                                widget: {
                                    xtype: 'button',
                                    margin: '3 0',
                                    padding: '1 2',
                                    border: 0,
                                    defaultBindProperty: null,
                                    handler: function (widgetColumn) {
                                        var record = widgetColumn.getWidgetRecord();
                                        var url = '/admin/coreshop/order-shipment/update-shipment-state',
                                            transitions = record.get('transitions'),
                                            id = record.get('o_id');
                                        if (transitions.length !== 0) {
                                            coreshop.order.order.state.changeState.showWindow(url, id, transitions, function (result) {
                                                if (result.success) {
                                                    this.reload();
                                                }
                                            }.bind(this));
                                        }
                                    }.bind(this),

                                    listeners: {
                                        beforerender: function (widgetColumn) {
                                            var record = widgetColumn.getWidgetRecord(),
                                                cursor = record.data.transitions.length > 0 ? 'pointer' : 'default';
                                            widgetColumn.setText(record.data.stateInfo.label);
                                            widgetColumn.setIconCls(record.data.transitions.length !== 0 ? 'pimcore_icon_open' : '');
                                            widgetColumn.setStyle('border-radius:2px; cursor:' + cursor + '; background-color:' + record.data.stateInfo.color + ';');
                                        }
                                    }
                                }
                            },
                            {
                                menuDisabled: true,
                                sortable: false,
                                xtype: 'actioncolumn',
                                width: 32,
                                items: [{
                                    iconCls: 'pimcore_icon_open',
                                    tooltip: t('open'),
                                    handler: function (grid, rowIndex) {
                                        coreshop.order.order.editShipment.showWindow(grid.getStore().getAt(rowIndex), function (result) {
                                            if (result.success) {
                                                this.reload();
                                            }
                                        }.bind(this));
                                    }.bind(this)
                                }]
                            }
                        ]
                    }
                ],
                tools: [
                    {
                        type: 'coreshop-add',
                        tooltip: t('add'),
                        handler: function () {
                            this.createShipment();
                        }.bind(this)
                    }
                ]
            });
        }

        return this.shippingInfo;
    },

    getInvoiceDetails: function () {
        if (!this.invoiceDetails) {
            this.invoicesStore = new Ext.data.JsonStore({
                data: this.sale.invoices
            });

            this.invoiceDetails = Ext.create('Ext.panel.Panel', {
                title: t('coreshop_invoices'),
                border: true,
                margin: '0 20 20 0',
                iconCls: 'coreshop_icon_orders_invoice',
                items: [
                    {
                        xtype: 'grid',
                        margin: '5 0 15 0',
                        cls: 'coreshop-detail-grid',
                        store: this.invoicesStore,
                        columns: [
                            {
                                xtype: 'gridcolumn',
                                flex: 1,
                                dataIndex: 'invoiceDate',
                                text: t('coreshop_invoice_date'),
                                renderer: function (val) {
                                    if (val) {
                                        return Ext.Date.format(new Date(val * 1000), t('coreshop_date_time_format'));
                                    }

                                    return '';
                                }
                            },
                            {
                                xtype: 'gridcolumn',
                                dataIndex: 'totalNet',
                                text: t('coreshop_total_without_tax'),
                                flex: 1,
                                renderer: coreshop.util.format.currency.bind(this, this.sale.currency.symbol)
                            },
                            {
                                xtype: 'gridcolumn',
                                dataIndex: 'totalGross',
                                text: t('coreshop_total'),
                                flex: 1,
                                renderer: coreshop.util.format.currency.bind(this, this.sale.currency.symbol)
                            },
                            {
                                xtype: 'widgetcolumn',
                                flex: 1,
                                widget: {
                                    xtype: 'button',
                                    margin: '3 0',
                                    padding: '1 2',
                                    border: 0,
                                    defaultBindProperty: null,
                                    handler: function (widgetColumn) {
                                        var record = widgetColumn.getWidgetRecord();
                                        var url = '/admin/coreshop/order-invoice/update-invoice-state',
                                            transitions = record.get('transitions'),
                                            id = record.get('o_id');
                                        if (transitions.length !== 0) {
                                            coreshop.order.order.state.changeState.showWindow(url, id, transitions, function (result) {
                                                if (result.success) {
                                                    this.reload();
                                                }
                                            }.bind(this));
                                        }
                                    }.bind(this),

                                    listeners: {
                                        beforerender: function (widgetColumn) {
                                            var record = widgetColumn.getWidgetRecord(),
                                                cursor = record.data.transitions.length > 0 ? 'pointer' : 'default';
                                            widgetColumn.setText(record.data.stateInfo.label);
                                            widgetColumn.setIconCls(record.data.transitions.length !== 0 ? 'pimcore_icon_open' : '');
                                            widgetColumn.setStyle('border-radius:2px; cursor:' + cursor + '; background-color:' + record.data.stateInfo.color + ';');
                                        }
                                    }
                                }
                            },
                            {
                                menuDisabled: true,
                                sortable: false,
                                xtype: 'actioncolumn',
                                width: 32,
                                items: [{
                                    iconCls: 'pimcore_icon_open',
                                    tooltip: t('open'),
                                    handler: function (grid, rowIndex) {
                                        coreshop.order.order.editInvoice.showWindow(grid.getStore().getAt(rowIndex), this.sale.currency, function (result) {
                                            if (result.success) {
                                                this.reload();
                                            }
                                        }.bind(this));
                                    }.bind(this)
                                }]
                            }
                        ]
                    }
                ],
                tools: [
                    {
                        type: 'coreshop-add',
                        tooltip: t('add'),
                        handler: function () {
                            this.createInvoice();
                        }.bind(this)
                    }
                ]
            });
        }

        return this.invoiceDetails;
    },

    updatePaymentInfoAlert: function () {
        if (this.paymentInfoAlert) {
            if (this.sale.totalPayed < this.sale.total || this.sale.totalPayed > this.sale.total) {
                this.paymentInfoAlert.update(t('coreshop_order_payment_paid_warning').format(coreshop.util.format.currency(this.sale.currency.symbol, this.sale.totalPayed), coreshop.util.format.currency(this.sale.currency.symbol, this.sale.totalGross)));
                this.paymentInfoAlert.show();
            } else {
                this.paymentInfoAlert.update('');
                this.paymentInfoAlert.hide();
            }
        }
    },

    getPaymentDetails: function () {

        if (!this.paymentInfo) {
            this.paymentsStore = new Ext.data.JsonStore({
                data: this.sale.payments
            });

            this.paymentInfoAlert = Ext.create('Ext.panel.Panel', {
                xtype: 'panel',
                cls: 'x-coreshop-alert',
                bodyPadding: 5,
                hidden: true
            });

            this.updatePaymentInfoAlert();

            var items = [
                this.paymentInfoAlert
            ];

            items.push({
                xtype: 'grid',
                margin: '0 0 15 0',
                cls: 'coreshop-detail-grid',
                store: this.paymentsStore,
                columns: [
                    {
                        xtype: 'gridcolumn',
                        dataIndex: 'datePayment',
                        text: t('date'),
                        flex: 1,
                        renderer: function (val) {
                            if (val) {
                                return Ext.Date.format(new Date(val * 1000), t('coreshop_date_time_format'));
                            }

                            return '';
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        flex: 1,
                        dataIndex: 'provider',
                        text: t('coreshop_paymentProvider')
                    },
                    {
                        xtype: 'gridcolumn',
                        dataIndex: 'amount',
                        text: t('coreshop_quantity'),
                        flex: 1,
                        renderer: function (value) {
                            return coreshop.util.format.currency(this.sale.currency.symbol, value);
                        }.bind(this)
                    },
                    /*{
                     menuDisabled: true,
                     sortable: false,
                     xtype: 'actioncolumn',
                     width: 32,
                     items: [{
                     iconCls: 'pimcore_icon_object',
                     tooltip: t('coreshop_show_transaction_notes'),
                     handler : function (grid, rowIndex) {
                     var record = grid.getStore().getAt(rowIndex);
                     this.showPaymentTransactions(record.get('transactionNotes'));
                     }.bind(this)
                     }]
                     },*/
                    {
                        xtype: 'widgetcolumn',
                        flex: 1,
                        widget: {
                            xtype: 'button',
                            margin: '3 0',
                            padding: '1 2',
                            border: 0,
                            defaultBindProperty: null,
                            handler: function (widgetColumn) {
                                var record = widgetColumn.getWidgetRecord();
                                var url = '/admin/coreshop/order-payment/update-payment-state',
                                    transitions = record.get('transitions'),
                                    id = record.get('id');
                                if (transitions.length !== 0) {
                                    coreshop.order.order.state.changeState.showWindow(url, id, transitions, function (result) {
                                        if (result.success) {
                                            this.reload();
                                        }
                                    }.bind(this));
                                }
                            }.bind(this),

                            listeners: {
                                beforerender: function (widgetColumn) {
                                    var record = widgetColumn.getWidgetRecord(),
                                        cursor = record.data.transitions.length > 0 ? 'pointer' : 'default';
                                    widgetColumn.setText(record.data.stateInfo.label);
                                    widgetColumn.setIconCls(record.data.transitions.length !== 0 ? 'pimcore_icon_open' : '');
                                    widgetColumn.setStyle('border-radius:2px; cursor:' + cursor + '; background-color:' + record.data.stateInfo.color + ';');
                                }
                            }
                        }
                    },
                    {
                        menuDisabled: true,
                        sortable: false,
                        xtype: 'actioncolumn',
                        width: 32,
                        items: [{
                            iconCls: 'pimcore_icon_open',
                            tooltip: t('open'),
                            handler: function (grid, rowIndex) {
                                coreshop.order.order.editPayment.showWindow(grid.getStore().getAt(rowIndex), function (result) {
                                    if (result.success) {
                                        this.reload();
                                    }
                                }.bind(this));
                            }.bind(this)
                        }]
                    }
                ],
            });

            this.paymentInfo = Ext.create('Ext.panel.Panel', {
                title: t('coreshop_payments'),
                border: true,
                margin: '0 20 20 0',
                iconCls: 'coreshop_icon_payment',
                tools: [
                    {
                        type: 'coreshop-add',
                        tooltip: t('add'),
                        handler: function () {
                            coreshop.order.order.createPayment.showWindow(this.sale.o_id, this.sale, function (result) {
                                if (result.success) {
                                    this.reload();
                                }
                            }.bind(this));
                        }.bind(this)
                    }
                ],
                items: items
            });
        }

        return this.paymentInfo;
    },

    createInvoice: function () {
        new coreshop.order.order.invoice(this.sale, function () {
            this.reload();
        }.bind(this));
    },

    createShipment: function () {
        new coreshop.order.order.shipment(this.sale, function () {
            this.reload();
        }.bind(this));
    },

    showPaymentTransactions: function (paymentTransactions) {
        if (paymentTransactions.length === 0) {
            Ext.Msg.alert(t('error'), t('coreshop_no_payment_transactions'));
            return false;
        }

        var transactionStore = new Ext.data.JsonStore({
            data: paymentTransactions
        });

        var itemsGrid = {
            xtype: 'grid',
            margin: '0 0 15 0',
            cls: 'coreshop-detail-grid',
            store: transactionStore,
            columns: [
                {
                    xtype: 'gridcolumn',
                    flex: 1,
                    dataIndex: 'date',
                    text: t('date'),
                    renderer: function (val) {
                        if (val) {
                            return Ext.Date.format(new Date(val * 1000), t('coreshop_date_time_format'));
                        }
                        return '';
                    }
                },
                {
                    xtype: 'gridcolumn',
                    dataIndex: 'title',
                    text: t('coreshop_transaction_id'),
                    flex: 1
                },
                {
                    xtype: 'gridcolumn',
                    dataIndex: 'description',
                    text: t('description'),
                    flex: 1
                }
            ]
        };

        var window = new Ext.window.Window({
            width: 600,
            height: 300,
            resizeable: false,
            layout: 'fit',
            items: [{
                xtype: 'form',
                bodyStyle: 'padding:20px 5px 20px 5px;',
                border: false,
                autoScroll: true,
                forceLayout: true,
                fieldDefaults: {
                    labelWidth: 150
                },
                buttons: [
                    {
                        text: t('close'),
                        handler: function (btn) {
                            window.destroy();
                            window.close();
                        },

                        iconCls: 'pimcore_icon_accept'
                    }
                ],
                items: itemsGrid
            }]
        });

        window.show();
        return window;
    },

    getCommentDetails: function () {

        if (this.commentInfo) {
            return this.commentInfo;
        }

        var orderCommentsModule = new coreshop.order.order.module.orderComments(this.sale)
        this.commentInfo = orderCommentsModule.getLayout();
        return this.commentInfo;
    }
});
