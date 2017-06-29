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

        if (this.order.invoiceCreationAllowed) {
            buttons.push({
                iconCls: 'coreshop_icon_orders_invoice',
                text: t('coreshop_invoice_create_short'),
                handler: function () {
                    this.createInvoice();
                }.bind(this)
            });
        }

        if (this.order.shipmentCreationAllowed) {
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

    getLeftItems: function() {
        return [
            this.getSaleInfo(),
            this.getShipmentDetails(),
            this.getInvoiceDetails(),
            this.getPaymentDetails(),
            this.getMailDetails()
        ];
    },

    getSaleInfo: function ($super) {
        if (!this.orderInfo) {
            var orderInfo = $super();

            this.orderStatesStore = new Ext.data.JsonStore({
                data: this.order.statesHistory
            });

            orderInfo.add({
                xtype: 'grid',
                margin: '0 0 15 0',
                cls: 'coreshop-detail-grid',
                store: this.orderStatesStore,
                columns: [
                    {
                        xtype: 'gridcolumn',
                        flex: 1,
                        dataIndex: 'title',
                        text: t('coreshop_orderstate'),
                        renderer: function (value, metaData) {

                            if (value) {
                                var bgColor = '';
                                //@fixme: add some colored circle to the left instead of heavy color stuff!
                                return '<span class="rounded-color" style="background-color:' + bgColor + ';"></span>' + value;
                            }

                            return '';
                        }
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

        return this.orderInfo;
    },

    getShipmentDetails: function () {
        if (!this.shippingInfo) {
            var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
                listeners: {
                    edit: function (editor, context, eOpts) {
                        var trackingCode = context.record.get('trackingCode');

                        Ext.Ajax.request({
                            url: '/admin/coreshop/order-shipment/change-tracking-code',
                            params: {
                                shipmentId: context.record.get("o_id"),
                                trackingCode: trackingCode
                            },
                            success: function (response) {
                                context.record.commit();

                            }.bind(this)
                        });
                    }.bind(this)
                }
            });

            this.shipmentsStore = new Ext.data.JsonStore({
                data: this.order.shipments
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
                        plugins: [
                            cellEditing
                        ],
                        columns: [
                            {
                                xtype: 'gridcolumn',
                                flex: 1,
                                dataIndex: 'weight',
                                text: t('coreshop_weight')
                            },
                            {
                                xtype: 'gridcolumn',
                                dataIndex: 'trackingCode',
                                text: t('coreshop_tracking_code'),
                                flex: 2,
                                field: {
                                    xtype: 'textfield'
                                }
                            },
                            {
                                menuDisabled: true,
                                sortable: false,
                                xtype: 'actioncolumn',
                                width: 32,
                                items: [{
                                    iconCls: 'pimcore_icon_edit',
                                    tooltip: t('Edit'),
                                    handler: function (grid, rowIndex, colIndex) {
                                        cellEditing.startEditByPosition({
                                            row: rowIndex,
                                            column: colIndex - 1
                                        });
                                    }.bind(this)
                                }]
                            },
                            {
                                xtype: 'widgetcolumn',
                                flex: 2,
                                widget: {
                                    xtype: 'button',
                                    margin: '5 0 5 0',
                                    padding: '3 4 3 4',
                                    _btnText: '',
                                    tooltip: t('open'),
                                    defaultBindProperty: null,
                                    handler: function (widgetColumn) {
                                        var record = widgetColumn.getWidgetRecord();
                                        pimcore.helpers.openObject(record.data.o_id, 'object');
                                    },
                                    listeners: {
                                        beforerender: function (widgetColumn) {
                                            var record = widgetColumn.getWidgetRecord();
                                            widgetColumn.setText(Ext.String.format(t('coreshop_shipment_order'), record.data.shipmentNumber));
                                        }
                                    }
                                }
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
                data: this.order.invoices
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
                                dataIndex: 'invoiceNumber',
                                text: t('coreshop_invoice_number'),
                                flex: 1
                            },
                            {
                                xtype: 'gridcolumn',
                                flex: 2,
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
                                flex: 2,
                                align: 'right',
                                renderer: coreshop.util.format.currency.bind(this, this.order.currency.symbol)
                            },
                            {
                                xtype: 'gridcolumn',
                                dataIndex: 'totalGross',
                                text: t('coreshop_total'),
                                flex: 2,
                                align: 'right',
                                renderer: coreshop.util.format.currency.bind(this, this.order.currency.symbol)
                            },
                            {
                                xtype: 'widgetcolumn',
                                flex: 2,
                                widget: {
                                    xtype: 'button',
                                    margin: '5 0 5 0',
                                    padding: '3 4 3 4',
                                    _btnText: '',
                                    defaultBindProperty: null,
                                    handler: function (widgetColumn) {
                                        var record = widgetColumn.getWidgetRecord();
                                        pimcore.helpers.openObject(record.data.o_id, 'object');
                                    },
                                    listeners: {
                                        beforerender: function (widgetColumn) {
                                            var record = widgetColumn.getWidgetRecord();
                                            widgetColumn.setText(Ext.String.format(t('coreshop_invoice_order'), record.data.invoiceNumber));
                                        }
                                    }
                                }
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
            if (this.order.totalPayed < this.order.total || this.order.totalPayed > this.order.total) {
                this.paymentInfoAlert.update(t('coreshop_order_payment_paid_warning').format(coreshop.util.format.currency(this.order.currency.symbol, this.order.totalPayed), coreshop.util.format.currency(this.order.currency.symbol, this.order.totalGross)));
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
                data: this.order.payments
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
                        flex: 1,
                        dataIndex: 'state',
                        text: t('state'),
                        renderer: function (val) {
                            if (val) {
                                return t('coreshop_payment_state_' + val);
                            }

                            return '';
                        }
                    },
                    {
                        xtype: 'gridcolumn',
                        dataIndex: 'transactionIdentifier',
                        text: t('coreshop_transactionNumber'),
                        flex: 1,
                        align: 'right'
                    },
                    {
                        xtype: 'gridcolumn',
                        dataIndex: 'amount',
                        text: t('coreshop_quantity'),
                        flex: 1,
                        renderer: function(value) {
                            return coreshop.util.format.currency(this.order.currency.symbol, value);
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
                ]
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
                            coreshop.order.order.createPayment.showWindow(this.order.o_id, this.order, function (result) {
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
    setWorkflowInfo: function () {

        var buttons = [],
            toolbar;

        //add reload function for worfklow manager!
        this.objectData.reload = this.reload.bind(this);

        if (this.objectData.workflowManagement) {
            this.objectData.data.workflowManagement = this.objectData.workflowManagement;
        }

        pimcore.elementservice.integrateWorkflowManagement('object', this.order.o_id, this.objectData, buttons);

        toolbar = new Ext.Toolbar({
            border: false,
            items: buttons,
            overflowHandler: 'scroller'
        });

        this.orderInfo.insert(0, toolbar);
    },

    createInvoice: function () {
        new coreshop.order.order.invoice(this.order, function () {
            this.reload();
        }.bind(this));
    },

    createShipment: function () {
        new coreshop.order.order.shipment(this.order, function () {
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
    }
});
