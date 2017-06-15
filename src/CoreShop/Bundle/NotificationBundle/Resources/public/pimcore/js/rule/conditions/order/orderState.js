/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.notification.rule.conditions.orderState');

coreshop.notification.rule.conditions.orderState = Class.create(coreshop.rules.conditions.abstract, {
    type: 'orderState',

    getForm: function () {
        this.form = Ext.create('Ext.form.FieldSet', {
            items: [
                {
                    xtype: 'combo',
                    fieldLabel: t('coreshop_transition_direction_mode'),
                    name: 'transitionType',
                    value: this.data ? this.data.transitionType : 3,
                    width: 250,
                    store: [[1, t('coreshop_transition_to')], [2, t('coreshop_transition_from')], [3, t('coreshop_transition_all')]],
                    triggerAction: 'all',
                    typeAhead: false,
                    editable: false,
                    forceSelection: true,
                    queryMode: 'local'
                },
                {
                    xtype: 'combo',
                    fieldLabel: t('coreshop_transition_direction_state'),
                    name: 'states',
                    value: this.data ? this.data.states : [],
                    width: 250,
                    store: pimcore.globalmanager.get('coreshop_order_states'),
                    triggerAction: 'all',
                    typeAhead: false,
                    editable: false,
                    forceSelection: true,
                    queryMode: 'local',
                    multiSelect: true,
                    displayField: 'label',
                    valueField: 'name'
                }
            ]
        });

        return this.form;
    }
});
