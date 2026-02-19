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

pimcore.registerNS('coreshop.order.sale.detail.blocks.carriage');
coreshop.order.order.detail.blocks.carriage = Class.create(coreshop.order.order.detail.abstractBlock, {
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
