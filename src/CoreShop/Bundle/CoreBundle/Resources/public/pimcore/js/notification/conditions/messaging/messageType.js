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

pimcore.registerNS('coreshop.notification.rule.conditions.messageType');

coreshop.notification.rule.conditions.messageType = Class.create(coreshop.rules.conditions.abstract, {
    type: 'messageType',

    getForm: function () {
        this.form = Ext.create('Ext.form.FieldSet', {
            items: [
                {
                    xtype: 'combo',
                    fieldLabel: t('coreshop_condition_messageType'),
                    name: 'messageType',
                    value: this.data ? this.data.messageType : null,
                    width: 250,
                    store: [['customer', t('coreshop_message_type_customer')], ['customer-reply', t('coreshop_message_type_customer_reply')], ['contact', t('coreshop_message_type_contact')]],
                    triggerAction: 'all',
                    typeAhead: false,
                    editable: false,
                    forceSelection: true,
                    queryMode: 'local'
                }
            ]
        });

        return this.form;
    }
});
