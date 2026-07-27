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

coreshop.order.order.shipment = Class.create(coreshop.order.order.shipment, {
    order: null,
    cb: null,

    createWindow: function ($super, shipAbleItems) {
        var window = $super(shipAbleItems),
            hasCarrier = this.order.shippingPayment.carrier !== null,
            orderCarrierId = parseInt(this.order.carrier),
            orderCarrierName = this.order.shippingPayment.carrier,
            showToolTip = true;

        var carrier = Ext.create('Ext.form.ComboBox', {
            xtype: 'combo',
            fieldLabel: t('coreshop_carrier'),
            mode: 'local',
            store: {
                type: 'coreshop_carriers'
            },
            displayField: 'identifier',
            valueField: 'id',
            forceSelection: true,
            triggerAction: 'all',
            name: 'carrier',
            value: orderCarrierId,
            afterLabelTextTpl: [
                '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>'
            ],
            allowBlank: false,
            required: true,
            listeners: {
                render: function (c) {
                    if (hasCarrier === true) {
                        new Ext.ToolTip({
                            target: c.getEl(),
                            html: t('coreshop_carrier_based_on_order').format(orderCarrierName),
                            listeners: {
                                beforeshow: {
                                    fn: function (el) {
                                        if (showToolTip === false) {
                                            return false;
                                        }
                                    }
                                }
                            }
                        });
                    }
                },
                change: function() {
                    showToolTip = false;
                }
            }
        });

        window.down('form').insert(0, carrier);

        return window;
    }
});
