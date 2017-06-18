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

pimcore.registerNS('coreshop.order.order.detail');
coreshop.order.order.detail = Class.create(coreshop.order.order.detail, {

    getLeftItems: function ($super) {
        var leftItems = $super();

        return leftItems.concat([this.getCarrierDetails()]);
    },

    getHeader: function ($super) {
        if (!this.headerPanel) {
            var header = $super();

            header.add({
                xtype: 'panel',
                html: t('coreshop_store') + '<br/><span class="coreshop_order_big">' + this.order.store.name + '</span>',
                bodyPadding: 20,
                flex: 1
            });

            return header;
        }

        return this.headerPanel;
    },

    getCarrierDetails: function () {
        if (!this.carrierDetails) {
            var items = [];

            items.push({
                xtype: 'panel',
                layout: 'hbox',
                items: [
                    {
                        xtype: 'panel',
                        flex: 1,
                        items: [
                            {
                                xtype: 'panel',
                                style: 'display:block',
                                text: t('coreshop_currency'),
                                html: '<span style="font-weight:bold;">' + t('coreshop_currency') + ': </span>' + this.order.currency.name
                            },
                            {
                                xtype: 'panel',
                                style: 'display:block',
                                text: t('coreshop_weight'),
                                html: '<span style="font-weight:bold;">' + t('coreshop_weight') + ': </span>' + (this.order.shippingPayment.weight ? this.order.shippingPayment.weight : 0)
                            }
                        ]
                    },
                    {
                        xtype: 'panel',
                        flex: 1,
                        items: [
                            {
                                xtype: 'panel',
                                style: 'display:block',
                                text: t('coreshop_carrier'),
                                html: '<span style="font-weight:bold;">' + t('coreshop_carrier') + ': </span>' + this.order.shippingPayment.carrier
                            },
                            {
                                xtype: 'panel',
                                style: 'display:block',
                                text: t('coreshop_price'),
                                html: '<span style="font-weight:bold;">' + t('coreshop_price') + ': </span>' + coreshop.util.format.currency(this.order.currency.symbol, this.order.shippingPayment.cost)
                            }
                        ]
                    }
                ]
            });

            this.carrierDetails = Ext.create('Ext.panel.Panel', {
                title: t('coreshop_carrier') + '/' + t('coreshop_paymentProvider'),
                margin: '0 20 20 0',
                border: true,
                flex: 6,
                iconCls: 'coreshop_icon_carrier',
                items: items
            });
        }

        return this.carrierDetails;
    }
});
