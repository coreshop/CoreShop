/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.order.sale.detail.blocks.info');
coreshop.order.order.detail.blocks.info = Class.create(coreshop.order.sale.detail.blocks.info, {
    saleStatesStore: null,

    initBlock: function ($super) {
        $super();

        var me = this;

        me.saleStatesStore = new Ext.data.JsonStore({
            data: []
        });
    },

    updateSale: function ($super) {
        $super();

        var me = this;

        me.saleInfo.removeAll();
        me.saleStatesStore.loadRawData(me.sale.statesHistory);

        if (me.sale.availableOrderTransitions.length > 0) {
            var buttons = [],
                changeStateRequest = function (context, btn, transitionInfo) {
                    btn.disable();
                    Ext.Ajax.request({
                        url: '/admin/coreshop/order/update-order-state',
                        params: {
                            transition: transitionInfo.transition,
                            o_id: context.sale.o_id
                        },
                        success: function (response) {
                            var res = Ext.decode(response.responseText);
                            if(res.success === true) {
                                me.panel.reload();
                            } else {
                                Ext.Msg.alert(t('error'), res.message);
                                btn.enable();
                            }
                        },
                        failure: function () {
                            btn.enable();
                        }
                    });
                };

            Ext.Array.each(me.sale.availableOrderTransitions, function (transitionInfo) {
                buttons.push({
                    xtype: 'button',
                    style: transitionInfo.transition === 'cancel' ? '' : 'background-color:#524646;border-left:10px solid ' + transitionInfo.color + ' !important;',
                    cls: transitionInfo.transition === 'cancel' ? 'coreshop_change_order_order_state_button coreshop_cancel_order_button' : 'coreshop_change_order_order_state_button',
                    text: transitionInfo.label,
                    handler: function (btn) {
                        if (transitionInfo.transition === 'cancel') {
                            Ext.MessageBox.confirm(t('info'), t('coreshop_cancel_order_confirm'), function (buttonValue) {
                                if (buttonValue === 'yes') {
                                    changeStateRequest(me, btn, transitionInfo);
                                }
                            });
                        } else {
                            changeStateRequest(me, btn, transitionInfo);
                        }
                    }
                })
            });

            me.saleInfo.add({
                xtype: 'panel',
                layout: 'hbox',
                margin: 0,
                items: buttons
            });
        }

        me.saleInfo.add({
            xtype: 'grid',
            margin: '0 0 15 0',
            cls: 'coreshop-detail-grid',
            store: me.saleStatesStore,
            plugins: [{
              ptype: 'rowexpander',
              rowBodyTpl : [
                '<div style="padding:0 0 10px 0;">',
                    '{description}',
                '</div>'
              ]
            }],
            columns: [
                {
                    xtype: 'gridcolumn',
                    flex: 1,
                    dataIndex: 'title',
                    text: t('coreshop_orderstate')
                },
                {
                    xtype: 'gridcolumn',
                    width: 250,
                    dataIndex: 'date',
                    text: t('date')
                }
            ]
        });
    }
});