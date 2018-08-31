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

pimcore.registerNS('coreshop.order.sale.detail.blocks.payment');
coreshop.order.order.detail.blocks.payment = Class.create(coreshop.order.sale.detail.abstractBlock, {
    initBlock: function () {
        var me = this;

        me.paymentsStore = new Ext.data.JsonStore({
            data: []
        });

        me.paymentInfoAlert = Ext.create('Ext.panel.Panel', {
            xtype: 'panel',
            cls: 'x-coreshop-alert',
            bodyPadding: 5,
            hidden: true
        });

        me.paymentInfo = Ext.create('Ext.panel.Panel', {
            title: t('coreshop_payments'),
            border: true,
            margin: '0 20 20 0',
            iconCls: 'coreshop_icon_payment',
            tools: [
                {
                    type: 'coreshop-add',
                    tooltip: t('add'),
                    handler: function () {
                        coreshop.order.order.createPayment.showWindow(me.sale.o_id, me.sale, function (result) {
                            if (result.success) {
                                me.panel.reload();
                            }
                        });
                    }
                }
            ],
            items: [
                me.paymentInfoAlert,
                {
                    xtype: 'grid',
                    margin: '0 0 15 0',
                    cls: 'coreshop-detail-grid',
                    store: me.paymentsStore,
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
                                    var url = '/admin/coreshop/order-payment/update-payment-state',
                                        transitions = record.get('transitions'),
                                        id = record.get('id');
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
                                    coreshop.order.order.editPayment.showWindow(grid.getStore().getAt(rowIndex), function (result) {
                                        if (result.success) {
                                            me.panel.reload();
                                        }
                                    });
                                }
                            }]
                        }
                    ]
                }
            ]
        });
    },

    updatePaymentInfoAlert: function () {
        var me = this;

        if (me.paymentInfoAlert) {
            if (me.sale.totalPayed < me.sale.total || me.sale.totalPayed > me.sale.total) {
                me.paymentInfoAlert.update(t('coreshop_order_payment_paid_warning').format(coreshop.util.format.currency(me.sale.currency.symbol, me.sale.totalPayed), coreshop.util.format.currency(me.sale.currency.symbol, me.sale.totalGross)));
                me.paymentInfoAlert.show();
            } else {
                me.paymentInfoAlert.update('');
                me.paymentInfoAlert.hide();
            }
        }
    },

    getPriority: function () {
        return 20;
    },

    getPosition: function () {
        return 'left';
    },

    getPanel: function () {
        return this.paymentInfo;
    },

    updateSale: function () {
        var me = this,
            tool = me.paymentInfo.tools.find(function(tool) { return tool.type === 'coreshop-add'; });

        me.paymentsStore.loadRawData(me.sale.payments);
        me.updatePaymentInfoAlert();

        if (me.sale.paymentCreationAllowed) {
            if (tool && Ext.isFunction(tool.show)) {
                tool.show();
            }
        } else {
            if (tool && Ext.isFunction(tool.hide)) {
                tool.hide();
            } else {
                tool.hidden = true;
            }
        }
    }
});