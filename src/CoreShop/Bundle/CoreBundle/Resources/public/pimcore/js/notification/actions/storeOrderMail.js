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
