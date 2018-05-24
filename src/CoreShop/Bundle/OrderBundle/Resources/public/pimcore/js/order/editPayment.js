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

pimcore.registerNS('coreshop.order.order.editPayment');
coreshop.order.order.editPayment = {

    showWindow: function (payment, callback) {
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

                                formValues['id'] = payment.getId();

                                Ext.Ajax.request({
                                    url: '/admin/coreshop/order-payment/update-payment',
                                    method: 'post',
                                    params: formValues,
                                    callback: function (request, success, response) {
                                        try {
                                            response = Ext.decode(response.responseText);

                                            if (response.success === true) {
                                                window.close();
                                                window.destroy();

                                                if (callback) {
                                                    callback(response);
                                                }
                                            } else if(response.success === false) {
                                                Ext.Msg.alert(t('error'), response.message);
                                            }
                                        }
                                        catch (e) {
                                            Ext.Msg.alert(t('error'), e);
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
                        value: payment.get('datePayment')
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: t('coreshop_payment_number'),
                        disabled: true,
                        value: payment.get('paymentNumber')
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: t('coreshop_paymentProvider'),
                        disabled: true,
                        value: payment.get('provider')
                    },
                    {
                        xtype: 'numberfield',
                        name: 'amount',
                        fieldLabel: t('coreshop_quantity'),
                        disabled: false,
                        value: payment.get('amount') / 100
                    },
                    {
                        xtype: 'gridpanel',
                        title: t('details'),
                        viewConfig: {
                        enableTextSelection: true
                        },
                        store: new Ext.data.ArrayStore({
                            data: payment.get('details'),
                            fields: ['name', 'value']
                        }),
                        columns: [
                            {
                                text: 'Name',
                                dataIndex: 'name',
                                flex: 1
                            },
                            {
                                text: 'Value',
                                dataIndex: 'value',
                                flex: 2,
                                renderer: function arg(val, test, test2){
                                    return '<div style="white-space: normal;">' + val + '</div>';
                                }
                            }
                        ]
                    }
                ]
            }]
        });

        window.show();
    }

};
