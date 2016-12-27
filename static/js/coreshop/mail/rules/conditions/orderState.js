/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.plugin.coreshop.mail.rules.conditions.orderState');

pimcore.plugin.coreshop.mail.rules.conditions.orderState = Class.create(pimcore.plugin.coreshop.rules.conditions.abstract, {
    type : 'orderState',

    getForm : function () {
        this.form = Ext.create('Ext.form.FieldSet', {
            items : [
                {
                    xtype: 'combo',
                    fieldLabel: t('coreshop_transition_direction'),
                    name: 'states',
                    value: this.data ? this.data.states : [],
                    width: 250,
                    store: pimcore.globalmanager.get("coreshop_order_states"),
                    triggerAction: 'all',
                    typeAhead: false,
                    editable: false,
                    forceSelection: true,
                    queryMode: 'local',
                    multiSelect : true,
                    displayField : 'label',
                    valueField : 'name'
                },
                {
                    xtype: 'combo',
                    fieldLabel: t('coreshop_transition_direction'),
                    name: 'transitionType',
                    value: this.data ? this.data.transitionType : 3,
                    width: 250,
                    store: [[1, t('coreshop_transition_to')], [2, t('coreshop_transition_from')], [3, t('coreshop_all')]],
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
