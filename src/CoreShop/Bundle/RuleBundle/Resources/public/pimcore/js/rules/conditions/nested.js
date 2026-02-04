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
