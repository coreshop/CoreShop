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

pimcore.registerNS('coreshop.filter.conditions.range');

coreshop.filter.conditions.range = Class.create(coreshop.filter.conditions.abstract, {

    type: 'range',

    getItems: function () {
        return [
            this.getFieldsComboBox(),
            {
                fieldLabel: t('coreshop_filters_step_count'),
                xtype: 'numberfield',
                name: 'stepCount',
                value: this.data.configuration.stepCount,
                width: 400,
                decimalPrecision: 2
            },
            {
                xtype: 'combo',
                fieldLabel: t('coreshop_filters_value_min'),
                name: 'preSelectMin',
                width: 400,
                store: this.valueStore,
                displayField: 'value',
                valueField: 'key',
                triggerAction: 'all',
                typeAhead: false,
                editable: false,
                forceSelection: true,
                queryMode: 'local',
                value: this.data.configuration.preSelectMin,
                plugins: ['clearbutton'],
            },
            {
                xtype: 'combo',
                fieldLabel: t('coreshop_filters_value_max'),
                name: 'preSelectMax',
                width: 400,
                store: this.valueStore,
                displayField: 'value',
                valueField: 'key',
                triggerAction: 'all',
                typeAhead: false,
                editable: false,
                forceSelection: true,
                queryMode: 'local',
                value: this.data.configuration.preSelectMax,
                plugins: ['clearbutton'],
            }
        ];
    }
});
