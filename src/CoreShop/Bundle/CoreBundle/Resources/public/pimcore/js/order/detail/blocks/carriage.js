/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.order.sale.detail.blocks.carriage');
coreshop.order.order.detail.blocks.carriage = Class.create(coreshop.order.sale.detail.abstractBlock, {
    saleInfo: null,

    initBlock: function () {
        var me = this;

        me.currencyPanel = Ext.create({
            xtype: 'panel',
            style: 'display:block',
            text: t('coreshop_currency')
        });

        me.weightPanel = Ext.create({
            xtype: 'panel',
            style: 'display:block',
            text: t('coreshop_weight')
        });

        me.carrierPanel = Ext.create({
            xtype: 'panel',
            style: 'display:block',
            text: t('coreshop_carrier')
        });

        me.pricePanel = Ext.create({
            xtype: 'panel',
            style: 'display:block',
            text: t('coreshop_price')
        });

        var items = [];

        items.push({
            xtype: 'panel',
            layout: 'hbox',
            items: [
                {
                    xtype: 'panel',
                    flex: 1,
                    items: [
                        me.currencyPanel,
                        me.weightPanel
                    ]
                },
                {
                    xtype: 'panel',
                    flex: 1,
                    items: [
                        me.carrierPanel,
                        me.pricePanel
                    ]
                }
            ]
        });

        this.carrierDetails = Ext.create('Ext.panel.Panel', {
            title: t('coreshop_order') + ': ' + t('coreshop_carrier') + '/' + t('coreshop_paymentProvider'),
            margin: '0 20 20 0',
            border: true,
            flex: 6,
            iconCls: 'coreshop_icon_carrier',
            items: items
        });
    },

    getPriority: function () {
        return 5;
    },

    getPosition: function () {
        return 'left';
    },

    getPanel: function () {
        return this.carrierDetails;
    },

    updateSale: function () {
        var me = this;

        me.currencyPanel.setHtml('<span style="font-weight:bold;">' + t('coreshop_currency') + ': </span>' + me.sale.currency.name);
        me.weightPanel.setHtml('<span style="font-weight:bold;">' + t('coreshop_weight') + ': </span>' + (me.sale.shippingPayment.weight ? me.sale.shippingPayment.weight : 0));
        me.carrierPanel.setHtml('<span style="font-weight:bold;">' + t('coreshop_carrier') + ': </span>' + me.sale.shippingPayment.carrier);
        me.pricePanel.setHtml('<span style="font-weight:bold;">' + t('coreshop_price') + ': </span>' + coreshop.util.format.currency(me.sale.currency.isoCode, me.sale.shippingPayment.cost));
    }
});
