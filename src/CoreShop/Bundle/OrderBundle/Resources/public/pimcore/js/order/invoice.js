/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.order.order.invoice');
coreshop.order.order.invoice = Class.create({
    order: null,
    cb: null,

    initialize: function (order, cb) {
        this.order = order;
        this.cb = cb;

        Ext.Ajax.request({
            url: '/admin/coreshop/order-invoice/get-invoice-able-items',
            params: {
                id: this.order.o_id
            },
            success: function (response) {
                var res = Ext.decode(response.responseText);

                if (res.success) {
                    if (res.items.length > 0) {
                        this.show(res.items);
                    }
                    else {
                        Ext.Msg.alert(t('coreshop_invoice'), t('coreshop_invoice_no_items'));
                    }
                } else {
                    Ext.Msg.alert(t('error'), res.message);
                }

            }.bind(this)
        });
    },

    show: function (invoiceAbleItems) {
        var positionStore = new Ext.data.JsonStore({
            data: invoiceAbleItems
        });

        var cellEditing = Ext.create('Ext.grid.plugin.CellEditing');

        var itemsGrid = {
            xtype: 'grid',
            padding: 10,
            cls: 'coreshop-order-detail-grid',
            store: positionStore,
            plugins: [cellEditing],
            listeners: {
                validateedit: function (editor, context) {
                    return context.value <= context.record.data.maxToInvoice;
                }
            },
            columns: [
                {
                    xtype: 'gridcolumn',
                    flex: 1,
                    dataIndex: 'name',
                    text: t('coreshop_product')
                },
                {
                    xtype: 'gridcolumn',
                    dataIndex: 'price',
                    text: t('coreshop_price'),
                    width: 100,
                    align: 'right',
                    renderer: coreshop.util.format.currency.bind(this, this.order.currency.symbol)
                },
                {
                    xtype: 'gridcolumn',
                    dataIndex: 'quantity',
                    text: t('coreshop_quantity'),
                    width: 100,
                    align: 'right'
                },
                {
                    xtype: 'gridcolumn',
                    dataIndex: 'quantityInvoiced',
                    text: t('coreshop_invoiced_quantity'),
                    width: 120,
                    align: 'right'
                },
                {
                    xtype: 'gridcolumn',
                    dataIndex: 'toInvoice',
                    text: t('coreshop_quantity_to_invoice'),
                    width: 100,
                    align: 'right',
                    field: {
                        xtype: 'numberfield',
                        decimalPrecision: 0
                    }
                },
                {
                    xtype: 'gridcolumn',
                    dataIndex: 'tax',
                    text: t('coreshop_tax'),
                    width: 100,
                    align: 'right',
                    renderer: coreshop.util.format.currency.bind(this, this.order.currency.symbol)
                },
                {
                    xtype: 'gridcolumn',
                    dataIndex: 'total',
                    text: t('coreshop_total'),
                    width: 100,
                    align: 'right',
                    renderer: coreshop.util.format.currency.bind(this, this.order.currency.symbol)
                }
            ]
        };

        var panel = Ext.create('Ext.panel.Panel', {
            title: t('coreshop_products'),
            border: true,
            iconCls: 'coreshop_icon_product',
            items: itemsGrid
        });

        var window = new Ext.window.Window({
            width: 800,
            height: 300,
            resizeable: true,
            modal: true,
            layout: 'fit',
            title: t('coreshop_invoice_create_new') + ' (' + this.order.o_id + ')',
            items: [panel],
            buttons: [
                {
                    text: t('save'),
                    iconCls: 'pimcore_icon_apply',
                    handler: function (btn) {
                        var itemsToInvoice = [];

                        positionStore.getRange().forEach(function (item) {
                            if (item.get("toInvoice") > 0) {
                                itemsToInvoice.push({
                                    orderItemId: item.get("orderItemId"),
                                    quantity: item.get("toInvoice")
                                });
                            }
                        });

                        window.setLoading(t('loading'));

                        Ext.Ajax.request({
                            url: '/admin/coreshop/order-invoice/create-invoice',
                            method: 'post',
                            params: {
                                'items': Ext.encode(itemsToInvoice),
                                'id': this.order.o_id
                            },
                            success: function (response) {
                                var res = Ext.decode(response.responseText);

                                if (res.success) {
                                    pimcore.helpers.showNotification(t('success'), t('success'), 'success');

                                    if (Ext.isFunction(this.cb)) {
                                        this.cb();
                                    }
                                } else {
                                    pimcore.helpers.showNotification(t('error'), t(res.message), 'error');
                                }

                                window.setLoading(false);
                                window.close();
                            }.bind(this)
                        });
                    }.bind(this)
                }
            ]
        });

        window.show();

        return window;
    }
});
