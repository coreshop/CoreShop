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

                                formValues['o_id'] = orderId;

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
