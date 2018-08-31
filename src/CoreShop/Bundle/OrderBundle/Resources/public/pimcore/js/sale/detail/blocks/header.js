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

pimcore.registerNS('coreshop.order.sale.detail.blocks.header');
coreshop.order.sale.detail.blocks.header = Class.create(coreshop.order.sale.detail.abstractBlock, {
    datePanel: null,
    totalPanel: null,
    productPanel: null,
    storePanel: null,

    initBlock: function () {
        var me = this;

        me.datePanel = Ext.create({
            xtype: 'panel',
            bodyPadding: 20,
            flex: 1
        });
        me.totalPanel = Ext.create({
            xtype: 'panel',
            bodyPadding: 20,
            flex: 1
        });
        me.productPanel = Ext.create({
            xtype: 'panel',
            bodyPadding: 20,
            flex: 1
        });
        me.storePanel = Ext.create({
            xtype: 'panel',
            bodyPadding: 20,
            flex: 1
        });
    },

    getPriority: function () {
        return 10;
    },

    getPosition: function () {
        return 'top';
    },

    getPanel: function () {
        var me = this,
            items = [
                me.datePanel,
                me.totalPanel,
                me.productPanel,
                me.storePanel
            ];

        me.headerPanel = Ext.create('Ext.panel.Panel', {
            layout: 'hbox',
            margin: 0,
            items: items
        });

        return me.headerPanel;
    },

    updateSale: function () {
        var me = this;

        me.datePanel.setHtml(t('coreshop_date') + '<br/><span class="coreshop_order_big">' + Ext.Date.format(new Date(me.sale.saleDate * 1000), t('coreshop_date_time_format')) + '</span>');
        me.totalPanel.setHtml(t('coreshop_sale_total') + '<br/><span class="coreshop_order_big">' + coreshop.util.format.currency(me.sale.currency.symbol, me.sale.totalGross) + '</span>');
        me.productPanel.setHtml(t('coreshop_product_count') + '<br/><span class="coreshop_order_big">' + me.sale.items.length + '</span>');
        me.storePanel.setHtml(t('coreshop_store') + '<br/><span class="coreshop_order_big">' + me.sale.store.name + '</span>');
    }
});