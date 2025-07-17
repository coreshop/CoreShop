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

pimcore.registerNS('coreshop.notification.rule.actions.storeOrderMail');

coreshop.notification.rule.actions.storeOrderMail = Class.create(coreshop.notification.rule.actions.storeMail, {
    type: 'storeOrderMail',

    fields: {},

    getForm: function ($super) {
        var form = $super();

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
            'sendShipments': this.sendShipments.getValue()
        }, values);

        return values;
    }
});
