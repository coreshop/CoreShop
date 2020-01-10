/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.order.sale.detail.blocks.invoice');
coreshop.order.order.detail.blocks.invoice = Class.create(coreshop.order.sale.detail.abstractBlock, {
    saleInfo: null,

    initBlock: function () {
        var me = this;

        me.invoicesStore = new Ext.data.JsonStore({
            data: []
        });

        me.invoiceDetails = Ext.create('Ext.panel.Panel', {
            title: t('coreshop_invoices'),
            border: true,
            margin: '0 20 20 0',
            iconCls: 'coreshop_icon_orders_invoice',
            items: [
                {
                    xtype: 'grid',
                    margin: '5 0 15 0',
                    cls: 'coreshop-detail-grid',
                    store: me.invoicesStore,
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
                            renderer: function (value) {
                                return coreshop.util.format.currency(me.sale.currency.symbol, value);
                            }
                        },
                        {
                            xtype: 'gridcolumn',
                            dataIndex: 'totalGross',
                            text: t('coreshop_total'),
                            flex: 1,
                            renderer: function (value) {
                                return coreshop.util.format.currency(me.sale.currency.symbol, value);
                            }
                        },
                        {
                            xtype: 'widgetcolumn',
                            flex: 1,
                            onWidgetAttach: function (col, widget, record) {
                                var cursor = record.data.transitions.length > 0 ? 'pointer' : 'default';

                                widget.setText(record.data.stateInfo.label);
                                widget.setIconCls(record.data.transitions.length !== 0 ? 'pimcore_icon_open' : '');

                                widget.setStyle('border-radius', '2px');
                                widget.setStyle('cursor', cursor);
                                widget.setStyle('background-color', record.data.stateInfo.color);
                            },
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
                                                me.panel.reload();
                                            }
                                        });
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
                                    coreshop.order.order.editInvoice.showWindow(grid.getStore().getAt(rowIndex), me.sale.currency, function (result) {
                                        if (result.success) {
                                            me.panel.reload();
                                        }
                                    });
                                }
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
                        me.createInvoice();
                    }.bind(this)
                }
            ]
        });

        me.topBarButton = Ext.create({
            xtype: 'button',
            iconCls: 'coreshop_icon_orders_invoice',
            text: t('coreshop_invoice_create_short'),
            hidden: true,
            handler: function () {
                me.createInvoice();
            }
        });
    },

    getTopBarItems: function () {
        var me = this;

        return [
            me.topBarButton
        ];
    },

    createInvoice: function () {
        var me = this;

        new coreshop.order.order.invoice(me.sale, function () {
            me.panel.reload();
        });
    },

    getPriority: function () {
        return 40;
    },

    getPosition: function () {
        return 'left';
    },

    getPanel: function () {
        return this.invoiceDetails;
    },

    updateSale: function () {
        var me = this,
            tool = me.invoiceDetails.tools.find(function(tool) { return tool.type === 'coreshop-add'; });

        me.invoicesStore.loadRawData(me.sale.invoices);

        if (me.sale.invoiceCreationAllowed) {
            me.topBarButton.show();
            if (tool && Ext.isFunction(tool.show)) {
                tool.show();
            }
        } else {
            me.topBarButton.hide();
            if (tool && Ext.isFunction(tool.hide)) {
                tool.hide();
            } else {
                tool.hidden = true;
            }
        }
    }
});
