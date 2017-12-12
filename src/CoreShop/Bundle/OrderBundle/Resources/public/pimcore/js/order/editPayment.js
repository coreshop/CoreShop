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
            width: 380,
            height: 350,
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
                        text: t('save'),
                        handler: function (btn) {
                            var form = btn.up('window').down('form').getForm();

                            if (form.isValid()) {
                                var formValues = form.getFieldValues();

                                formValues['id'] = payment.getId();

                                Ext.Ajax.request({
                                    url: '/admin/coreshop/order/update-payment',
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
                        fieldLabel: t('coreshop_date'),
                        name: 'date',
                        disabled: true,
                        value: payment.get("datePayment")
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: t('coreshop_paymentProvider'),
                        disabled: true,
                        value: payment.get("provider")
                    },
                    {
                        xtype: 'textfield',
                        name: 'transactionNumber',
                        fieldLabel: t('coreshop_transactionNumber'),
                        disabled: true,
                        value: payment.get("transactionIdentifier")
                    },
                    {
                        xtype: 'numberfield',
                        name: 'amount',
                        fieldLabel: t('coreshop_quantity'),
                        disabled: false,
                        value: payment.get("amount") / 100
                    },
                    {
                        xtype: 'combo',
                        fieldLabel: t('coreshop_state'),
                        name: 'state',
                        value: payment.get("state"),
                        store: [
                            ['new', t('coreshop_payment_state_new')],
                            ['processing', t('coreshop_payment_state_processing')],
                            ['completed', t('coreshop_payment_state_completed')],
                            ['failed', t('coreshop_payment_state_failed')],
                            ['canceled', t('coreshop_payment_state_canceled')],
                            ['refunded', t('coreshop_payment_state_refunded')],
                            ['unknown', t('coreshop_payment_state_unknown')]
                        ],
                        triggerAction: 'all',
                        typeAhead: false,
                        editable: false,
                        forceSelection: true,
                        queryMode: 'local'

                    }
                ]
            }]
        });

        window.show();
    }

};
