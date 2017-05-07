/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.plugin.coreshop.orders.shipment');
pimcore.plugin.coreshop.orders.shipment = Class.create({
    order : null,
    cb : null,

    initialize: function (order, cb) {
        this.order = order;
        this.cb = cb;

        Ext.Ajax.request({
            url: '/admin/CoreShop/order-shipment/get-ship-able-items',
            params: {
                id : this.order.o_id
            },
            success: function (response) {
                var res = Ext.decode(response.responseText);

                if(res.success) {
                    if(res.items.length > 0) {
                        this.show(res.items);
                    }
                    else {
                        Ext.Msg.alert(t('coreshop_shipment'), t('coreshop_shipment_no_items'));
                    }
                } else {
                    Ext.Msg.alert(t('error'), res.message);
                }
            }.bind(this)
        });
    },

    show : function (shipAbleItems) {
        var positionStore = new Ext.data.JsonStore({
            data : shipAbleItems
        });

        var cellEditing = Ext.create('Ext.grid.plugin.CellEditing');



        var itemsGrid = {
            xtype : 'grid',
            cls : 'coreshop-order-detail-grid',
            store :  positionStore,
            plugins: [cellEditing],
            listeners : {
                validateedit : function(editor, context) {
                    return context.value <= context.record.data.maxToShip;
                }
            },
            columns : [
                {
                    xtype : 'gridcolumn',
                    flex : 1,
                    dataIndex : 'name',
                    text : t('coreshop_product')
                },
                {
                    xtype : 'gridcolumn',
                    dataIndex : 'price',
                    text : t('coreshop_price'),
                    width : 100,
                    align : 'right',
                    renderer: coreshop.util.format.currency.bind(this, this.order.currency.symbol)
                },
                {
                    xtype : 'gridcolumn',
                    dataIndex : 'quantity',
                    text : t('coreshop_quantity'),
                    width : 100,
                    align : 'right'
                },
                {
                    xtype : 'gridcolumn',
                    dataIndex : 'quantityShipped',
                    text : t('coreshop_shipped_quantity'),
                    width : 120,
                    align : 'right'
                },
                {
                    xtype : 'gridcolumn',
                    dataIndex : 'toShip',
                    text : t('coreshop_quantity_to_ship'),
                    width : 100,
                    align : 'right',
                    field : {
                        xtype: 'numberfield',
                        decimalPrecision : 0
                    }
                }
            ]
        };

        var trackingCode = Ext.create('Ext.form.TextField', {
            fieldLabel:t('coreshop_tracking_code'),
            name : 'trackingCode'
        });

        pimcore.globalmanager.get("coreshop_carriers").load();

        var carrier = Ext.create('Ext.form.ComboBox', {
            xtype:'combo',
            fieldLabel:t('coreshop_carrier'),
            mode: 'local',
            store: pimcore.globalmanager.get("coreshop_carriers"),
            displayField: 'name',
            valueField: 'id',
            forceSelection: true,
            triggerAction: 'all',
            name:'carrierId',
            value : parseInt(this.order.carrier),
            afterLabelTextTpl: [
                '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>'
            ],
            allowBlank: false,
            required : true
        });

        var panel = Ext.create('Ext.panel.Panel', {
            title : t('coreshop_products'),
            border : true,
            iconCls : 'coreshop_icon_product',
            items : [
                {
                    xtype : 'panel',
                    border :false,
                    padding : 10,
                    items : [carrier, trackingCode, itemsGrid]
                }
            ]
        });

        var window = new Ext.window.Window({
            width: 800,
            height: 400,
            resizeable: true,
            modal : true,
            layout : 'fit',
            title : t('coreshop_shipment_create_new') + ' (' + this.order.o_id + ')',
            items : [panel],
            buttons: [
                {
                    text: t('save'),
                    iconCls: 'pimcore_icon_apply',
                    handler: function (btn) {
                        var itemsToShip = [];

                        positionStore.getRange().forEach(function(item) {
                            if(item.get("toShip") > 0) {
                                itemsToShip.push({
                                    orderItemId : item.get("orderItemId"),
                                    quantity : item.get("toShip")
                                });
                            }
                        });

                        window.setLoading(t('loading'));

                        Ext.Ajax.request({
                            url: '/admin/CoreShop/order-shipment/create-shipment',
                            method : 'post',
                            params: {
                                'items' : Ext.encode(itemsToShip),
                                'id' : this.order.o_id,
                                'carrier' : carrier.getValue(),
                                'trackingCode' : trackingCode.getValue()
                            },
                            success: function (response) {
                                var res = Ext.decode(response.responseText);

                                if (res.success) {
                                    pimcore.helpers.showNotification(t('success'), t('success'), 'success');

                                    pimcore.helpers.openObject(res.shipmentId, 'object');

                                    if(Ext.isFunction(this.cb)) {
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
