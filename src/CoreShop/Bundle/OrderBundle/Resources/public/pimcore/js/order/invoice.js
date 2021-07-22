/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.order.order.invoice');
coreshop.order.order.invoice = Class.create({
    order: null,
    cb: null,

    height: 400,
    width: 800,

    initialize: function (order, cb) {
        this.order = order;
        this.cb = cb;

        Ext.Ajax.request({
            url: Routing.generate('coreshop_admin_order_invoice_get_processable_items'),
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

    getStoreFields: function() {
        return [
            'orderItemId',
            'price',
            'maxToInvoice',
            'quantity',
            'quantityInvoiced',
            'toInvoice',
            'tax',
            'total',
            'name'
        ];
    },

    getGridColumns: function() {
        return [
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
                renderer: coreshop.util.format.currency.bind(this, this.order.currency.isoCode)
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
                renderer: coreshop.util.format.currency.bind(this, this.order.currency.isoCode)
            },
            {
                xtype: 'gridcolumn',
                dataIndex: 'total',
                text: t('coreshop_total'),
                width: 100,
                align: 'right',
                renderer: coreshop.util.format.currency.bind(this, this.order.currency.isoCode)
            }
        ];
    },

    createWindow: function(invoiceAbleItems) {
        var me = this;

        var positionStore = new Ext.data.JsonStore({
            data: invoiceAbleItems,
            fields: this.getStoreFields()
        });

        var rowEditing = Ext.create('Ext.grid.plugin.RowEditing');

        var itemsGrid = {
            xtype: 'grid',
            cls: 'coreshop-order-detail-grid',
            minHeight: 400,
            store: positionStore,
            plugins: [rowEditing],
            listeners: {
                validateedit: function (editor, context) {
                    if (context.field === 'toInvoice') {
                        return context.value <= context.record.data.maxToInvoice;
                    }

                    return true;
                }
            },
            columns: this.getGridColumns()
        };

        var panel = Ext.create('Ext.form.Panel', {
            title: t('coreshop_invoice'),
            border: true,
            iconCls: 'coreshop_icon_product',
            bodyPadding: 10,
            items: [itemsGrid]
        });

        var window = new Ext.window.Window({
            width: me.width,
            height: me.height,
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
                            if (item.get('toInvoice') > 0) {
                                itemsToInvoice.push(me.processItemsToInvoice(item));
                            }
                        });

                        window.setLoading(t('loading'));

                        var data = panel.getForm().getFieldValues();
                        data['id'] = parseInt(this.order.o_id);
                        data['items'] = itemsToInvoice;

                        Ext.Ajax.request({
                            url: Routing.generate('coreshop_admin_order_invoice_create'),
                            method: 'post',
                            jsonData: data,
                            success: function (response) {
                                var res = Ext.decode(response.responseText);

                                if (res.success) {
                                    pimcore.helpers.showNotification(t('success'), t('success'), 'success');

                                    if (Ext.isFunction(this.cb)) {
                                        this.cb();
                                    }

                                    window.close();
                                } else {
                                    pimcore.helpers.showNotification(t('error'), t(res.message), 'error');
                                }

                                window.setLoading(false);
                            }.bind(this)
                        });
                    }.bind(this)
                }
            ]
        });

        return window;
    },

    processItemsToInvoice: function(item) {
        return {
            orderItemId: item.get("orderItemId"),
            quantity: item.get("toInvoice")
        };
    },

    show: function (invoiceAbleItems) {
        var grWindow = this.createWindow(invoiceAbleItems);

        grWindow.show();

        return window;
    }
});
