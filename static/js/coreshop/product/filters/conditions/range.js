/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (http://www.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.plugin.coreshop.filters.conditions.range');

pimcore.plugin.coreshop.filters.conditions.range = Class.create(pimcore.plugin.coreshop.filters.conditions.abstract, {

    type : 'range',

    getItems : function ()
    {
        return [
            {
                xtype: 'combo',
                fieldLabel: t('coreshop_product_filters_value_min'),
                name: 'preSelectMin',
                width: 400,
                store: this.valueStore,
                displayField : 'key',
                valueField : 'value',
                triggerAction: 'all',
                typeAhead: false,
                editable: false,
                forceSelection: true,
                queryMode: 'local',
                value : this.data.preSelectMin
            },
            {
                xtype: 'combo',
                fieldLabel: t('coreshop_product_filters_value_max'),
                name: 'preSelectMax',
                width: 400,
                store: this.valueStore,
                displayField : 'key',
                valueField : 'value',
                triggerAction: 'all',
                typeAhead: false,
                editable: false,
                forceSelection: true,
                queryMode: 'local',
                value : this.data.preSelectMax
            }
        ];
    }
});
