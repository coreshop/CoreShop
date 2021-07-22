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
