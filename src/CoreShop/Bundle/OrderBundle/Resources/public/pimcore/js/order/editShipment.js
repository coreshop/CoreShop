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
                        text: t('save'),
                        handler: function (btn) {
                            var form = btn.up('window').down('form').getForm();

                            if (form.isValid()) {
                                var formValues = form.getFieldValues();

                                formValues['id'] = shipment.get('o_id');

                                Ext.Ajax.request({
                                    url: '/admin/coreshop/order-shipment/update-shipment',
                                    method: 'post',
                                    params: formValues,
                                    callback: function (request, success, response) {
                                        try {
                                            response = Ext.decode(response.responseText);

                                            if (response.success) {
                                                window.close();
                                                window.destroy();

                                                if (callback) {
                                                    callback(response);
                                                }
                                            } else {
                                                //pimcore returns a error window. don't do this twice.
                                                //Ext.Msg.alert(t('error'), response.message);
                                            }
                                        }
                                        catch (e) {
                                            //pimcore returns a error window. don't do this twice.
                                        }
                                    }
                                });
                            }
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
                        fieldLabel: t('coreshop_tracking_code'),
                        name: 'trackingCode',
                        disabled: false,
                        value: shipment.get('trackingCode')
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
                        fieldLabel: t('coreshop_carrier'),
                        disabled: true,
                        value: shipment.get('carrierName')
                    },
                    {
                        xtype: 'combo',
                        fieldLabel: t('state'),
                        name: 'state',
                        value: shipment.get('state'),
                        store: [
                            ['ready', t('coreshop_shipment_state_ready')],
                            ['cancelled', t('coreshop_shipment_state_cancelled')],
                            ['shipped', t('coreshop_shipment_state_shipped')]
                        ],
                        triggerAction: 'all',
                        typeAhead: false,
                        editable: true,
                        forceSelection: true,
                        queryMode: 'local'
                    },
                    {
                        xtype: 'button',
                        fieldLabel: '',
                        style: 'margin: 5px 0;',
                        tooltip: t('open'),
                        handler: function (widgetColumn) {
                            pimcore.helpers.openObject(shipment.get('o_id'), 'object');
                        },
                        listeners: {
                            beforerender: function (widgetColumn) {
                                widgetColumn.setText(Ext.String.format(t('coreshop_shipment_order'), shipment.get('shipmentNumber')));
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
