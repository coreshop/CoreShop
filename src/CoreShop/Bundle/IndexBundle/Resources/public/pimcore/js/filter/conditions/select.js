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

pimcore.registerNS('coreshop.filter.conditions.select');

coreshop.filter.conditions.select = Class.create(coreshop.filter.conditions.abstract, {

    type: 'select',

    getItems: function () {
        return [
            this.getFieldsComboBox(),
            {
                xtype: 'combo',
                fieldLabel: t('coreshop_filters_value'),
                name: 'preSelect',
                width: 400,
                store: this.valueStore,
                displayField: 'value',
                valueField: 'key',
                triggerAction: 'all',
                typeAhead: false,
                editable: false,
                forceSelection: true,
                queryMode: 'local',
                value: this.data.configuration.preSelect,
                plugins: ['clearbutton'],
            }
        ];
    }
});
