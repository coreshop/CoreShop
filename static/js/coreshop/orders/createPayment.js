/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.plugin.coreshop.orders.createPayment');
pimcore.plugin.coreshop.orders.createPayment = {

    showWindow : function (tab) {
        var orderId = tab.id;

        var paymentProvidersStore = new Ext.data.Store({
            proxy : {
                type : 'ajax',
                url : '/plugin/CoreShop/admin_order/get-payment-providers',
                reader : {
                    type : 'json',
                    rootProperty : 'data'
                }
            },
            fields : ['id', 'name']
        });
        paymentProvidersStore.load();

        var date = Ext.form.DateField();

        var combo = Ext.form.ComboBox();

        var transactionNumber = Ext.form.TextField();

        var amount = Ext.form.NumberField({

        });

        var window = new Ext.window.Window({
            width : 380,
            height : 300,
            resizeable : false,
            layout : 'fit',
            items : [{
                xtype : 'form',
                bodyStyle:'padding:20px 5px 20px 5px;',
                border: false,
                autoScroll: true,
                forceLayout: true,
                fieldDefaults: {
                    labelWidth: 150
                },
                buttons: [
                    {
                        text: 'Save',
                        handler: function (btn) {
                            var form = btn.up('window').down('form').getForm();

                            if (form.isValid()) {
                                var formValues = form.getFieldValues();

                                formValues['o_id'] = orderId;

                                Ext.Ajax.request({
                                    url : '/plugin/CoreShop/admin_order/add-payment',
                                    method : 'post',
                                    params : formValues,
                                    callback: function (request, success, response) {
                                        try {
                                            response = Ext.decode(response.responseText);

                                            if (response.success) {
                                                window.close();
                                                window.destroy();

                                                tab.reload(tab.data.currentLayoutId);
                                            } else {
                                                Ext.Msg.alert(t('error'), response.message);
                                            }
                                        }
                                        catch (e) {
                                            //TODO: Something went wrong dialog
                                        }
                                    }
                                });
                            }
                        },

                        iconCls: 'pimcore_icon_apply'
                    }
                ],
                items : [
                    {
                        xtype : 'datefield',
                        fieldLabel: t('coreshop_date'),
                        name: 'date',
                        value: new Date(),
                        afterLabelTextTpl: [
                            '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>'
                        ],
                        allowBlank: false
                    },
                    {
                        xtype:'combo',
                        fieldLabel:t('coreshop_paymentProvider'),
                        typeAhead:true,
                        mode:'local',
                        listWidth:100,
                        store:paymentProvidersStore,
                        displayField:'name',
                        valueField:'id',
                        forceSelection:true,
                        triggerAction:'all',
                        name:'paymentProvider',
                        afterLabelTextTpl: [
                            '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>'
                        ],
                        allowBlank: false
                    },
                    {
                        xtype : 'textfield',
                        name : 'transactionNumber',
                        fieldLabel : t('coreshop_transactionNumber')
                    },
                    {
                        xtype : 'numberfield',
                        name : 'amount',
                        fieldLabel : t('coreshop_amount'),
                        minValue: 0,
                        decimalPrecision : 4,
                        value : tab.data.data.total - tab.data.data.totalPayed,
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
