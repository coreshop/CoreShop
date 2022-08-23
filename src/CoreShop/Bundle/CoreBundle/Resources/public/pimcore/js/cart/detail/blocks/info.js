/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
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
