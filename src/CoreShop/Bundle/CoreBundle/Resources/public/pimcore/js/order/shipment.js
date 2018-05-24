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

coreshop.order.order.shipment = Class.create(coreshop.order.order.shipment, {
    order: null,
    cb: null,

    show: function ($super, shipAbleItems) {
        pimcore.globalmanager.get('coreshop_carriers').load();

        var window = $super(shipAbleItems),
            store = pimcore.globalmanager.get('coreshop_carriers'),
            hasCarrier = this.order.shippingPayment.carrier !== null,
            orderCarrierId = parseInt(this.order.carrier),
            orderCarrierName = this.order.shippingPayment.carrier,
            showToolTip = true;

        var carrier = Ext.create('Ext.form.ComboBox', {
            xtype: 'combo',
            fieldLabel: t('coreshop_carrier'),
            mode: 'local',
            store: store,
            displayField: 'name',
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
                },
                afterrender: function () {
                    if (hasCarrier === true) {
                        var orderCarrierIndex;
                        if (store.isLoaded()) {
                            orderCarrierIndex = store.findExact('id', orderCarrierId);
                            if (orderCarrierIndex !== -1) {
                                this.setValue(store.getAt(orderCarrierIndex));
                            }
                        } else {
                            store.load();
                            store.on('load', function (store, records, options) {
                                orderCarrierIndex = store.findExact('id', orderCarrierId);
                                if (orderCarrierIndex !== -1) {
                                    this.setValue(store.getAt(orderCarrierIndex));
                                }
                            }.bind(this));
                        }
                    }
                }
            }
        });

        window.down('form').insert(0, carrier);

        return window;
    }
});
