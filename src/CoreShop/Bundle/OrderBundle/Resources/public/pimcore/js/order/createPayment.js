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

pimcore.registerNS('coreshop.order.order.createPayment');
coreshop.order.order.createPayment = {

    showWindow: function (id, order, callback) {
        var orderId = id;

        var paymentProvidersStore = new Ext.data.Store({
            proxy: {
                type: 'ajax',
                url: '/admin/coreshop/order/get-payment-providers',
                reader: {
                    type: 'json',
                    rootProperty: 'data'
                }
            },
            fields: ['id', 'name']
        });
        paymentProvidersStore.load();

        var window = new Ext.window.Window({
            width: 380,
            height: 380,
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

                                formValues['o_id'] = orderId;

                                Ext.Ajax.request({
                                    url: '/admin/coreshop/order/add-payment',
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

                                                //tab.reload(tab.data.currentLayoutId);
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
                        value: new Date(),
                        afterLabelTextTpl: [
                            '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>'
                        ],
                        allowBlank: false
                    },
                    {
                        xtype: 'combo',
                        fieldLabel: t('coreshop_paymentProvider'),
                        typeAhead: true,
                        mode: 'local',
                        listWidth: 100,
                        store: paymentProvidersStore,
                        displayField: 'name',
                        valueField: 'id',
                        forceSelection: true,
                        triggerAction: 'all',
                        name: 'paymentProvider',
                        afterLabelTextTpl: [
                            '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>'
                        ],
                        allowBlank: false
                    },
                    {
                        xtype: 'textfield',
                        name: 'transactionNumber',
                        fieldLabel: t('coreshop_transactionNumber'),
                        afterLabelTextTpl: [
                            '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>'
                        ],
                        allowBlank: false
                    },
                    {
                        xtype: 'combo',
                        fieldLabel: t('coreshop_state'),
                        name: 'state',
                        value: 'new',
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
                        queryMode: 'local',
                        allowBlank: false,
                        afterLabelTextTpl: [
                            '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>'
                        ]
                    },
                    {
                        xtype: 'numberfield',
                        name: 'amount',
                        fieldLabel: t('coreshop_amount'),
                        decimalPrecision: 4,
                        value: order.total - order.totalPayed,
                        afterLabelTextTpl: [
                            '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>'
                        ],
                        allowBlank: false
                    }
                ]
            }]
        });

        window.show();
    }

};
