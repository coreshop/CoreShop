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

pimcore.registerNS('coreshop.notification.rule.conditions.abstractTransition');

coreshop.notification.rule.conditions.abstractTransition = Class.create(coreshop.rules.conditions.abstract, {
    getRepoName: function() {
        return '';
    },

    getForm: function () {
        this.form = Ext.create('Ext.form.FieldSet', {
            items: [
                {
                    xtype: 'combo',
                    fieldLabel: t('coreshop_transition_to'),
                    name: 'transition',
                    value: this.data ? this.data.transition : [],
                    width: 250,
                    store: pimcore.globalmanager.get(this.getRepoName()),
                    triggerAction: 'all',
                    typeAhead: false,
                    editable: false,
                    forceSelection: true,
                    queryMode: 'local',
                    displayField: 'name',
                    valueField: 'name'
                }
            ]
        });

        return this.form;
    }
});
