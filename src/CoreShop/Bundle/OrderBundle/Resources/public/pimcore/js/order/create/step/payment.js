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

pimcore.registerNS('coreshop.order.order.create.step.payment');
coreshop.order.order.create.step.payment = Class.create(coreshop.order.order.create.abstractStep, {

    isValid: function () {
        return this.getValues().paymentProvider;
    },

    getPriority: function () {
        return 50;
    },

    getValues: function (parent) {
        return this.panel.getForm().getFieldValues();
    },

    reset: function() {
        this.panel.getForm().reset();
    },

    getPanel: function () {
        this.panel = Ext.create('Ext.form.Panel', {
            items: [
                Ext.create({
                    xtype: 'combo',
                    fieldLabel: t('coreshop_payment_provider'),
                    editable: false,
                    mode: 'local',
                    listWidth: 100,
                    store: {
                        type: 'coreshop_payment_provider'
                    },
                    displayField: 'identifier',
                    valueField: 'id',
                    triggerAction: 'all',
                    labelWidth: 150,
                    name: 'paymentProvider',
                    listeners: {
                    change: function() {
                        this.eventManager.fireEvent('payment_provider.changed');
                        this.eventManager.fireEvent('validation');
                    }.bind(this)
                }
                })
            ]
        })
        ;

        return this.panel;
    },

    getName: function () {
        return t('coreshop_order_create_payment');
    },

    getIconCls: function() {
        return 'coreshop_icon_payment_provider';
    }
});
