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

pimcore.registerNS('coreshop.order.order.create.step.shipping');
coreshop.order.order.create.step.shipping = Class.create(coreshop.order.order.create.abstractStep, {
    carriersStore: null,

    initStep: function () {
        this.carriersStore = new Ext.data.JsonStore({
            data: []
        });
    },

    isValid: function (parent) {
        var values = this.getValues();

        return values.carrier;
    },

    getPriority: function () {
        return 50;
    },

    setPreviewData: function(data) {
        if (data.shippingAddress && data.invoiceAddress && data.items.length > 0) {
            this.layout.show();

            if (data.carriers) {
                this.carriersStore.loadData(data.carriers);

                this.panel.down('[name=carrier]').setValue(data.carrier);
            }
        }
        else {
            this.panel.down('[name=carrier]').setValue(null);
            this.layout.hide();
        }
    },

    reset: function() {
        this.panel.getForm().reset();
        this.layout.hide();
    },

    getValues: function (parent) {
        return this.panel.getForm().getFieldValues();
    },

    getPanel: function () {
        var deliveryCarrierChoose = Ext.create({
            xtype: 'combo',
            fieldLabel: t('coreshop_carrier'),
            name: 'carrier',
            store: this.carriersStore,
            editable: false,
            triggerAction: 'all',
            queryMode: 'local',
            width: 500,
            displayField: 'name',
            valueField: 'id',
            listeners: {
                change: function (combo, value) {
                    this.eventManager.fireEvent('preview');
                }.bind(this)
            }
        });

        this.panel = Ext.create('Ext.form.Panel', {
            items: [
                deliveryCarrierChoose
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
    }
});
