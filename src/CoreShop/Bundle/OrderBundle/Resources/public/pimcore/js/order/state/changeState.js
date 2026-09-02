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

pimcore.registerNS('coreshop.order.order.state.changeState');
coreshop.order.order.state.changeState = {

    showWindow: function (url, id, transitions, callback) {
        var buttons = [],
            window;

        Ext.Array.each(transitions, function (transitionInfo) {
            buttons.push({
                xtype: 'button',
                text: transitionInfo.label,
                border: 0,
                style: 'background-color:#524646;border-left:10px solid ' + transitionInfo.color + ' !important;',
                handler: function (btn) {
                    btn.setDisabled(true);
                    Ext.Ajax.request({
                        url: url,
                        params: {
                            id: id,
                            transition: transitionInfo.transition
                        },
                        success: function (response) {
                            var res = Ext.decode(response.responseText);
                            if (res.success) {
                                window.close();
                                window.destroy();
                                if (callback) {
                                    callback(res);
                                }
                            } else {
                                Ext.Msg.alert(t('error'), res.message);
                            }
                        }.bind(this),
                        failure: function (response) {
                            btn.setDisabled(false);
                        }.bind(this)
                    });
                }
            });
        });

        window = new Ext.window.Window({
            width: 450,
            height: 170,
            modal: true,
            resizeable: false,
            title: t('coreshop_change_state'),
            layout: 'fit',
            items: [{
                xtype: 'label',
                margin: '20 0 20 20',
                border: 0,
                text: t('coreshop_change_state_description')
            },
                {
                    xtype: 'fieldcontainer',
                    layout: 'hbox',
                    margin: '30 20 20 20',
                    border: 0,
                    style: {
                        border: 0
                    },
                    items: buttons
                }]
        });

        window.show();
    }

};
