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

pimcore.registerNS('coreshop.notification.rule.actions.orderMail');

coreshop.notification.rule.actions.orderMail = Class.create(coreshop.notification.rule.actions.mail, {

    type: 'orderMail',

    fields: {},

    getForm: function ($super) {
        var form = $super(),
            me = this;

        this.doNotSendToDesignatedRecipient = Ext.create({
            fieldLabel: t('coreshop_mail_rule_do_not_send_to_designated_recipient'),
            xtype: 'checkbox',
            name: 'doNotSendToDesignatedRecipient',
            checked: this.data ? this.data.doNotSendToDesignatedRecipient : false
        });

        this.sendInvoices = Ext.create({
            fieldLabel: t('coreshop_mail_rule_send_invoices'),
            xtype: 'checkbox',
            name: 'sendInvoices',
            checked: this.data ? this.data.sendInvoices : false
        });

        this.sendShipments = Ext.create({
            fieldLabel: t('coreshop_mail_rule_send_shipments'),
            xtype: 'checkbox',
            name: 'sendShipments',
            checked: this.data ? this.data.sendShipments : false
        });

        form.add([this.sendInvoices, this.sendShipments]);

        return form;
    },

    getValues: function ($super) {
        var values = $super();

        values = Ext.applyIf({
            'sendInvoices': this.sendInvoices.getValue(),
            'sendShipments': this.sendShipments.getValue(),
            'doNotSendToDesignatedRecipient': this.doNotSendToDesignatedRecipient.getValue()
        }, values);

        return values;
    }
});
