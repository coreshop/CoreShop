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

pimcore.registerNS('coreshop.filter.conditions');
pimcore.registerNS('coreshop.filter.conditions.abstract');

coreshop.filter.conditions.abstract = Class.create(coreshop.filter.abstract, {
    elementType: 'conditions',

    getDefaultItems: function () {
        var quantityUnitStore = pimcore.helpers.quantityValue.getClassDefinitionStore();
        quantityUnitStore.on("load", function (store) {
            store.insert(0,
                {
                    'abbreviation': t('empty'),
                    'id': 0
                }
            )
        });

        return [
            {
                xtype: 'textfield',
                name: 'label',
                width: 400,
                fieldLabel: t('label'),
                value: this.data.label
            },
            {
                xtype: 'combobox',
                name: 'quantityUnit',
                triggerAction: "all",
                editable: false,
                width: 400,
                fieldLabel: t('coreshop_filters_quantityUnit'),
                store: quantityUnitStore,
                value: this.data.quantityUnit ? this.data.quantityUnit : 0,
                displayField: 'abbreviation',
                valueField: 'id'
            }
        ];
    }
});
