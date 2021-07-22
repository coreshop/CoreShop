/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
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
