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

pimcore.registerNS('coreshop.notification.rule.conditions.backendCreated');

coreshop.notification.rule.conditions.backendCreated = Class.create(coreshop.rules.conditions.abstract, {
    type: 'backendCreated',

    getForm: function () {
        this.form = new Ext.form.Panel({
            items: [
                {
                    xtype: 'checkbox',
                    fieldLabel: t('coreshop_condition_backendCreated'),
                    name: 'backendCreated',
                    checked: this.data ? this.data.backendCreatedbackendCreated : false
                }
            ]
        });

        return this.form;
    }
});
