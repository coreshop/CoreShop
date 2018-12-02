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

pimcore.registerNS('coreshop.order.order.detail.blocks.header');
coreshop.order.order.detail.blocks.header = Class.create(coreshop.order.sale.detail.blocks.header, {
    orderState: null,
    paymentState: null,
    shipmentState: null,
    invoiceState: null,

    initBlock: function ($super) {
        $super();

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
    },

    getPanel: function ($super) {
        var me = this,
            items = [
                me.orderState,
                me.paymentState,
                me.shipmentState,
                me.invoiceState
            ],
            panelDefault = $super();

        var panelItemsStates = Ext.create('Ext.panel.Panel', {
            layout: 'hbox',
            margin: 0,
            items: items
        });

        return Ext.create('Ext.panel.Panel', {
            border: false,
            margin: '0 0 20 0',
            items: [panelItemsStates, panelDefault]
        });
    },

    updateSale: function ($super) {
        $super();

        var me = this;

        me.orderState.setHtml(t('coreshop_workflow_name_coreshop_order') + '<br/><span class="coreshop_order_big order_state"><span class="color-dot" style="background-color:' + this.sale.orderState.color + ';"></span> ' + this.sale.orderState.label + '</span>');
        me.paymentState.setHtml(t('coreshop_workflow_name_coreshop_order_payment') + '<br/><span class="coreshop_order_medium"><span class="color-dot" style="background-color:' + this.sale.orderPaymentState.color + ';"></span>' + this.sale.orderPaymentState.label + '</span>');
        me.shipmentState.setHtml(t('coreshop_workflow_name_coreshop_order_shipment') + '<br/><span class="coreshop_order_medium"><span class="color-dot" style="background-color:' + this.sale.orderShippingState.color + ';"></span>' + this.sale.orderShippingState.label + '</span>');
        me.invoiceState.setHtml(t('coreshop_workflow_name_coreshop_order_invoice') + '<br/><span class="coreshop_order_medium"><span class="color-dot" style="background-color:' + this.sale.orderInvoiceState.color + ';"></span>' + this.sale.orderInvoiceState.label + '</span>');
    }
});