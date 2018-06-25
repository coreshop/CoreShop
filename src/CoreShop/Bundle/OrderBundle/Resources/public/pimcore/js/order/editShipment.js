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

pimcore.registerNS('coreshop.order.order.editShipment');
coreshop.order.order.editShipment = {

    showWindow: function (shipment, callback) {
        var window = new Ext.window.Window({
            width: 600,
            height: 450,
            resizeable: false,
            modal: true,
            layout: 'fit',
            items: [{
                xtype: 'form',
                bodyStyle: 'padding:20px 5px 20px 5px;',
                border: false,
                autoScroll: true,
                forceLayout: true,
                anchor: '100%',
                fieldDefaults: {
                    labelWidth: 150
                },
                buttons: [
                    {
                        text: t('OK'),
                        handler: function (btn) {
                            window.close();
                            window.destroy();
                        },
                        iconCls: 'pimcore_icon_apply'
                    }
                ],
                items: [
                    {
                        xtype: 'datefield',
                        format: 'd.m.Y H:i',
                        altFormats: 'U',
                        fieldLabel: t('coreshop_date'),
                        name: 'date',
                        disabled: true,
                        value: shipment.get('shipmentDate')
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: t('coreshop_shipment_number'),
                        name: 'shipmentNumber',
                        disabled: true,
                        value: shipment.get('shipmentNumber')
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: t('coreshop_tracking_code'),
                        name: 'trackingCode',
                        disabled: true,
                        value: shipment.get('trackingCode')
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: t('coreshop_weight'),
                        name: 'weight',
                        disabled: true,
                        value: shipment.get('weight')
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: t('coreshop_carrier'),
                        disabled: true,
                        value: shipment.get('carrierName')
                    },
                    {
                        xtype: 'button',
                        fieldLabel: '',
                        style: 'margin: 5px 0;',
                        tooltip: t('open'),
                        handler: function (widgetColumn) {
                            pimcore.helpers.openObject(shipment.get('o_id'), 'object');
                            window.close();
                        },
                        listeners: {
                            beforerender: function (widgetColumn) {
                                widgetColumn.setText(Ext.String.format(t('coreshop_open_order_shipment'), shipment.get('shipmentNumber')));
                            }
                        }
                    },
                    {
                        xtype: 'gridpanel',
                        title: t('coreshop_products'),
                        editable: true,
                        store: new Ext.data.JsonStore({
                            data: shipment.get('items'),
                            fields: ['_itemName', 'quantity']
                        }),
                        columns: [
                            {text: 'Item', dataIndex: '_itemName', flex: 2 },
                            {text: 'Quantity', dataIndex: 'quantity', flex: 1 }
                        ]
                    }
                ]
            }]
        });

        window.show();
    }

};
