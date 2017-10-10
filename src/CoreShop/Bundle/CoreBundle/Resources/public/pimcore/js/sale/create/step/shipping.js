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

pimcore.registerNS('coreshop.order.sale.create.step.shipping');
coreshop.order.sale.create.step.shipping = Class.create(coreshop.order.sale.create.abstractStep, {
    carriersStore: null,

    initStep: function () {
        var me = this;

        me.eventManager.on('products.changed', function () {
            me.reloadCarriers();
        });
        me.eventManager.on('address.changed', function () {
            me.reloadCarriers();
        });

        this.carriersStore = new Ext.data.JsonStore({
            data: []
        });
    },

    isValid: function (parent) {
        var values = this.getValues();

        return values.carrier;
    },

    getPriority: function () {
        return 60;
    },

    getValues: function (parent) {
        return this.panel.getForm().getFieldValues();
    },

    getPanel: function () {
        var deliveryCarrierChoose = Ext.create({
            xtype: 'combo',
            fieldLabel: t('coreshop_carrier'),
            name: "carrier",
            store: this.carriersStore,
            editable: false,
            triggerAction: 'all',
            queryMode: "local",
            width: 500,
            displayField: 'name',
            valueField: 'id',
            listeners: {
                change: function (combo, value) {
                    var carrier = this.carriersStore.getById(value);

                    deliveryPriceField.setValue(carrier.get("priceFormatted"));

                    this.eventManager.fireEvent('carrier.changed');
                    this.eventManager.fireEvent('totals.reload');
                    this.eventManager.fireEvent('validation');
                }.bind(this)
            }
        });

        var deliveryPriceField = Ext.create({
            xtype: 'textfield',
            value: 0,
            disabled: true,
            fieldLabel: t('coreshop_price')
        });

        this.panel = Ext.create('Ext.form.Panel', {
            items: [
                deliveryCarrierChoose,
                deliveryPriceField
            ]
        });

        return this.panel;
    },

    getName: function () {
        return t('coreshop_order_create_shipping');
    },

    getIconCls: function () {
        return 'coreshop_icon_shipping';
    },

    getLayout: function ($super) {
        var layout = $super();

        layout.hide();

        return layout;
    },

    reloadCarriers: function () {
        var values = this.creationPanel.getValues();

        if (values.shippingAddress && values.invoiceAddress && values.products.length > 0) {
            this.layout.show();
            this.layout.setLoading(t("loading"));

            Ext.Ajax.request({
                url: '/admin/coreshop/' + this.creationPanel.type + '-creation/get-carrier-details',
                method: 'post',
                jsonData: values,
                callback: function (request, success, response) {
                    try {
                        response = Ext.decode(response.responseText);

                        if (response.success) {
                            this.carriersStore.loadData(response.carriers);
                        } else {
                            Ext.Msg.alert(t('error'), response.message);
                        }
                    }
                    catch (e) {
                        Ext.Msg.alert(t('error'), e);
                    }

                    this.layout.setLoading(false);
                }.bind(this)
            });
        } else {
            this.layout.hide();
        }
    }
});