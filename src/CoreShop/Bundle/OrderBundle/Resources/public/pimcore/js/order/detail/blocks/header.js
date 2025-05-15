/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

pimcore.registerNS('coreshop.order.order.detail.blocks.header');
coreshop.order.order.detail.blocks.header = Class.create(coreshop.order.order.detail.abstractBlock, {
    datePanel: null,
    totalPanel: null,
    productPanel: null,
    storePanel: null,
    orderState: null,
    paymentState: null,
    shipmentState: null,
    invoiceState: null,

    initBlock: function () {
        var me = this;

        me.orderState = Ext.create({
            xtype: 'panel',
            bodyPadding: 20,
            flex: 1
        });

        me.paymentState = Ext.create({
            xtype: 'panel',
            bodyPadding: 20,
            flex: 1
        });

        me.shipmentState = Ext.create({
            xtype: 'panel',
            bodyPadding: 20,
            flex: 1
        });

        me.invoiceState = Ext.create({
            xtype: 'panel',
            bodyPadding: 20,
            flex: 1
        });
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
            items1 = [
                me.datePanel,
                me.totalPanel,
                me.productPanel,
                me.storePanel,
            ],
            items2 = [
                me.orderState,
                me.paymentState,
                me.shipmentState,
                me.invoiceState
            ];

        me.headerPanel = Ext.create('Ext.panel.Panel', {
            layout: 'hbox',
            margin: 0,
            items: items1
        });

        me.panelItemsStates = Ext.create('Ext.panel.Panel', {
            layout: 'hbox',
            margin: 0,
            items: items2
        });

        return Ext.create('Ext.panel.Panel', {
            border: false,
            margin: '0 0 20 0',
            items: [me.panelItemsStates, me.headerPanel]
        });
    },

    updateSale: function ($super) {
        $super();

        var me = this;

        me.datePanel.setHtml(t('coreshop_date') + '<br/><span class="coreshop_order_big">' + Ext.Date.format(new Date(me.sale.saleDate * 1000), t('coreshop_date_time_format')) + '</span>');

        if (me.sale.currency.id === me.sale.baseCurrency.id) {
            me.totalPanel.setHtml(t('coreshop_sale_total') + '<br/><span class="coreshop_order_big">' + coreshop.util.format.currency(me.sale.currency.isoCode, me.sale.totalGross) + '</span>');
        }
        else {
            me.totalPanel.setHtml(
                t('coreshop_sale_total') +
                '<br/><span class="coreshop_order_big">' +
                coreshop.util.format.currency(me.sale.baseCurrency.isoCode, me.sale.totalGross) +
                ' / ' +
                coreshop.util.format.currency(me.sale.currency.isoCode, me.sale.convertedTotalGross) +
                '</span>');
        }

        me.productPanel.setHtml(t('coreshop_product_count') + '<br/><span class="coreshop_order_big">' + me.sale.items.length + '</span>');
        me.storePanel.setHtml(t('coreshop_store') + '<br/><span class="coreshop_order_big">' + me.sale.store.name + '</span>');
        me.orderState.setHtml(t('coreshop_workflow_name_coreshop_order') + '<br/><span class="coreshop_order_big order_state"><span class="color-dot" style="background-color:' + this.sale.orderState.color + ';"></span> ' + this.sale.orderState.label + '</span>');
        me.paymentState.setHtml(t('coreshop_workflow_name_coreshop_order_payment') + '<br/><span class="coreshop_order_medium"><span class="color-dot" style="background-color:' + this.sale.orderPaymentState.color + ';"></span>' + this.sale.orderPaymentState.label + '</span>');
        me.shipmentState.setHtml(t('coreshop_workflow_name_coreshop_order_shipment') + '<br/><span class="coreshop_order_medium"><span class="color-dot" style="background-color:' + this.sale.orderShippingState.color + ';"></span>' + this.sale.orderShippingState.label + '</span>');
        me.invoiceState.setHtml(t('coreshop_workflow_name_coreshop_order_invoice') + '<br/><span class="coreshop_order_medium"><span class="color-dot" style="background-color:' + this.sale.orderInvoiceState.color + ';"></span>' + this.sale.orderInvoiceState.label + '</span>');
    }
});
