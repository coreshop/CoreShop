/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

pimcore.registerNS('coreshop.order.order.createPayment');
coreshop.order.order.createPayment = {

    showWindow: function (id, order, callback) {
        var orderId = id;

        var paymentProvidersStore = new Ext.data.Store({
            proxy: {
                type: 'ajax',
                url: Routing.generate('coreshop_payment_provider_list'),
                reader: {
                    type: 'json',
                    rootProperty: 'data'
                }
            },
            fields: ['id', 'identifier']
        });
        paymentProvidersStore.load();

        var window = new Ext.window.Window({
            width: 380,
            height: 380,
            modal: true,
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

                                formValues['id'] = orderId;

                                window.setLoading(t('loading'));

                                Ext.Ajax.request({
                                    url: Routing.generate('coreshop_admin_order_payment_add'),
                                    method: 'post',
                                    params: formValues,
                                    callback: function (request, success, response) {
                                        window.setLoading(false);

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
                        displayField: 'identifier',
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
                        xtype: 'numberfield',
                        name: 'amount',
                        fieldLabel: t('coreshop_amount'),
                        decimalPrecision: 2,
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
