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

pimcore.registerNS('coreshop.order.order.detail.blocks.shipment');
coreshop.order.order.detail.blocks.shipment = Class.create(coreshop.order.order.detail.abstractBlock, {
    initBlock: function () {
        var me = this;

        me.shipmentsStore = new Ext.data.JsonStore({
            data: []
        });

        me.shippingInfo = Ext.create('Ext.panel.Panel', {
            title: t('coreshop_shipments'),
            border: true,
            margin: '0 20 20 0',
            iconCls: 'coreshop_icon_orders_shipment',
            items: [
                {
                    xtype: 'grid',
                    margin: '0 0 15 0',
                    cls: 'coreshop-detail-grid',
                    store: me.shipmentsStore,
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
                                    var url = '/admin/coreshop/order-shipment/update-shipment-state',
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
                                    coreshop.order.order.editShipment.showWindow(grid.getStore().getAt(rowIndex), function (result) {
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
                        me.createShipment();
                    }
                }
            ]
        });

        me.topBarButton = Ext.create({
            xtype: 'button',
            iconCls: 'coreshop_icon_orders_shipment',
            text: t('coreshop_shipment_create_short'),
            handler: function () {
                me.createShipment();
            }
        });

    },

    getTopBarItems: function () {
        var me = this;

        return [
            me.topBarButton
        ];
    },

    createShipment: function () {
        var me = this;

        new coreshop.order.order.shipment(me.sale, function () {
            me.panel.reload();
        });
    },

    getPriority: function () {
        return 30;
    },

    getPosition: function () {
        return 'left';
    },

    getPanel: function () {
        return this.shippingInfo;
    },

    updateSale: function () {
        var me = this,
            tool = me.shippingInfo.tools.find(function(tool) { return tool.type === 'coreshop-add'; });

        me.shipmentsStore.loadRawData(me.sale.shipments);

        if (me.sale.shipmentCreationAllowed) {
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
