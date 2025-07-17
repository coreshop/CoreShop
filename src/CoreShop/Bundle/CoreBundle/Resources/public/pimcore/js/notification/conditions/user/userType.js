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

pimcore.registerNS('coreshop.notification.rule.conditions.userType');

coreshop.notification.rule.conditions.userType = Class.create(coreshop.rules.conditions.abstract, {
    type: 'userType',

    getForm: function () {
        this.form = Ext.create('Ext.form.FieldSet', {
            items: [
                {
                    xtype: 'combo',
                    fieldLabel: t('coreshop_condition_userType'),
                    name: 'userType',
                    value: this.data ? this.data.userType : null,
                    width: 250,
                    store: [
                        ['register', t('coreshop_user_type_new')],
                        ['password-reset', t('coreshop_user_type_password')],
                        ['newsletter-double-opt-in', t('coreshop_user_type_newsletter_double_opt_in')],
                        ['newsletter-confirmed', t('coreshop_user_type_newsletter_confirmed')]
                    ],
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
