/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
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
