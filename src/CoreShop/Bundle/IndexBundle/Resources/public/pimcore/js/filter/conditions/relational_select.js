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

pimcore.registerNS('coreshop.filter.conditions.relational_select');

coreshop.filter.conditions.relational_select = Class.create(coreshop.filter.conditions.abstract, {
    type: 'relational_select',

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
                value: this.data.configuration.preSelects
            }
        ];
    }
});
