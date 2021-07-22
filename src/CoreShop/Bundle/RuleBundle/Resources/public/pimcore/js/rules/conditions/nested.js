/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.rules.conditions.nested');
coreshop.rules.conditions.nested = Class.create(coreshop.rules.conditions.abstract, {

    type: 'nested',

    operatorCombo: null,
    conditions: null,

    getForm: function () {
        var me = this;

        this.conditions = new this.parent.__proto__.constructor(this.parent.conditions);

        var layout = this.conditions.getLayout();

        // add saved conditions
        if (this.data && this.data.conditions) {
            Ext.each(this.data.conditions, function (condition) {
                this.conditions.addCondition(condition.type, condition, false);
            }.bind(this));
        }


        this.form = new Ext.form.Panel({
            items: [
                layout
            ]
        });

        return this.form;
    },

    getTopBarItems: function () {
        if (!this.operatorCombo) {
            this.operatorCombo = Ext.create(
                {
                    xtype: 'combo',
                    fieldLabel: t('coreshop_condition_conditions_operator'),
                    name: 'operator',
                    width: 500,
                    store: [['and', t('coreshop_condition_conditions_operator_and')], ['or', t('coreshop_condition_conditions_operator_or')], ['not', t('coreshop_condition_conditions_operator_not')]],
                    triggerAction: 'all',
                    typeAhead: false,
                    editable: false,
                    forceSelection: true,
                    queryMode: 'local',
                    value: this.data ? this.data.operator : 'and'
                }
            );
        }

        return ['-', this.operatorCombo];
    },

    getValues: function () {
        return {
            operator: this.operatorCombo.getValue(),
            conditions: this.conditions.getConditionsData()
        };
    }
});
