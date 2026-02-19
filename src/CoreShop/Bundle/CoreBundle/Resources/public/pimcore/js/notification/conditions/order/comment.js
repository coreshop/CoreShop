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

pimcore.registerNS('coreshop.notification.rule.conditions.comment');

coreshop.notification.rule.conditions.comment = Class.create(coreshop.rules.conditions.abstract, {
    type: 'comment',
    getForm: function () {
        this.form = new Ext.form.Panel({
            items: [
                {
                    xtype: 'combo',
                    fieldLabel: t('coreshop_condition_comment_action'),
                    typeAhead: false,
                    editable: false,
                    width: 500,
                    value: this.data ? this.data.commentAction : null,
                    store: [['create', t('coreshop_condition_comment_action_create')]],
                    forceSelection: true,
                    triggerAction: 'all',
                    name: 'commentAction',
                    queryMode: 'local'
                }
            ]
        });

        return this.form;
    }
});
