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

pimcore.registerNS('coreshop.order.cart.detail.blocks.info');
coreshop.order.cart.detail.blocks.info = Class.create(coreshop.order.cart.detail.blocks.info, {

    initBlock: function ($super) {
        $super();

        var me = this;

        me.carrierInfo = Ext.create('Ext.panel.Panel', {
            border: true,
            flex: 6,
            iconCls: 'coreshop_icon_carrier',
        });
    },

    updateSale: function ($super) {
        var me = this;

        $super();

        me.carrierInfo.removeAll();

        if (me.sale.carrierInfo) {
            me.saleInfo.add({
                xtype: 'label',
                style: 'font-weight:bold;display:block',
                text: t('coreshop_carrier')
            });
            me.saleInfo.add({
                xtype: 'panel',
                html: me.sale.carrierInfo.name
            })
        }
    }
});
