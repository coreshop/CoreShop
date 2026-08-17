/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

pimcore.registerNS('coreshop.order.order.message');
coreshop.order.order.message = {

    showWindow: function (tab) {
        var orderId = tab.id;

        var message = new Ext.form.TextArea({
            xtype: 'textarea',
            name: 'message',
            style: "font-family: 'Courier New', Courier, monospace;",
            width: '100%',
            height: '100%'
        });

        var window = new Ext.window.Window({
            width: 380,
            height: 300,
            resizeable: false,
            layout: 'fit',
            title: t('coreshop_order_new_message'),
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
                        text: 'Save',
                        handler: function (btn) {
                            var form = btn.up('window').down('form').getForm();

                            if (form.isValid()) {
                                var formValues = form.getFieldValues();

                                formValues['id'] = orderId;

                                Ext.Ajax.request({
                                    url: '/admin/coreshop/order/send-message',
                                    method: 'post',
                                    params: formValues,
                                    callback: function (request, success, response) {
                                        try {
                                            response = Ext.decode(response.responseText);

                                            if (response.success) {
                                                window.close();
                                                window.destroy();
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
                items: [
                    message
                ]
            }]
        });

        window.show();
    }

};
