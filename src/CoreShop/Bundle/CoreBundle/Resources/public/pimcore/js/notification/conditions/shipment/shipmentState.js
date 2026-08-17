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

pimcore.registerNS('coreshop.notification.rule.conditions.shipmentState');

coreshop.notification.rule.conditions.shipmentState = Class.create(coreshop.rules.conditions.abstract, {
    type: 'shipmentState',

    getForm: function () {
        this.form = Ext.create('Ext.form.FieldSet', {
            items: [
                {
                    xtype: 'combo',
                    fieldLabel: t('coreshop_transition_direction_state'),
                    name: 'shipmentState',
                    value: this.data ? this.data.shipmentState : [],
                    width: 250,
                    store: pimcore.globalmanager.get('coreshop_states_shipment'),
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
