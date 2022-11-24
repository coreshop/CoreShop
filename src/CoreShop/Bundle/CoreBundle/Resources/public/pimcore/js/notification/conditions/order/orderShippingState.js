/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

pimcore.registerNS('coreshop.notification.rule.conditions.orderShippingState');

coreshop.notification.rule.conditions.orderShippingState = Class.create(coreshop.rules.conditions.abstract, {
    type: 'shippingState',

    getForm: function () {
        this.form = Ext.create('Ext.form.FieldSet', {
            items: [
                {
                    xtype: 'combo',
                    fieldLabel: t('coreshop_transition_direction_state'),
                    name: 'orderShippingState',
                    value: this.data ? this.data.orderShippingState : [],
                    width: 250,
                    store: pimcore.globalmanager.get('coreshop_states_order_shipment'),
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
