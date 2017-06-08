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

pimcore.registerNS('pimcore.plugin.coreshop.filters.conditions.multiselect');

pimcore.plugin.coreshop.filters.conditions.multiselect = Class.create(pimcore.plugin.coreshop.filters.conditions.abstract, {

    type: 'multiselect',

    getItems: function () {
        return [
            {
                xtype: 'combo',
                fieldLabel: t('coreshop_product_filters_values'),
                name: 'preSelects',
                width: 400,
                store: this.valueStore,
                displayField: 'key',
                multiSelect: true,
                valueField: 'value',
                triggerAction: 'all',
                typeAhead: false,
                editable: false,
                forceSelection: true,
                queryMode: 'local',
                value: this.data.preSelects
            }
        ];
    }
});
