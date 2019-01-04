/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
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
