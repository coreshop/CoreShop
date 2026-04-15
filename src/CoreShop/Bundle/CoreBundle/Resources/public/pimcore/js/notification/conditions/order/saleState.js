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

pimcore.registerNS('coreshop.notification.rule.conditions.saleState');

coreshop.notification.rule.conditions.saleState = Class.create(coreshop.rules.conditions.abstract, {
    type: 'saleState',

    getForm: function () {
        this.form = Ext.create('Ext.form.FieldSet', {
            items: [
                {
                    xtype: 'combo',
                    fieldLabel: t('coreshop_transition_direction_state'),
                    name: 'saleState',
                    value: this.data ? this.data.saleState : [],
                    width: 250,
                    store: pimcore.globalmanager.get('coreshop_states_order_sales_type'),
                    triggerAction: 'all',
                    typeAhead: false,
                    editable: false,
                    forceSelection: true,
                    queryMode: 'local',
                    displayField: 'label',
                    valueField: 'state'
                }
            ]
        });

        return this.form;
    }
});
