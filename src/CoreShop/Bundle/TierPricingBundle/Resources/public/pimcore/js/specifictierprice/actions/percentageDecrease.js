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

pimcore.registerNS('coreshop.tier_pricing.specific_tier_price.actions.percentage_decrease');
coreshop.tier_pricing.specific_tier_price.actions.percentage_decrease = Class.create(coreshop.tier_pricing.specific_tier_price.actions.abstract, {
    type: 'percentage_decrease',

    getGridColumns: function() {
        return [
            {
                text: t('coreshop_tier_percentage'),
                flex: 1,
                sortable: false,
                dataIndex: 'percentage',
                name: 'tier_percentage',
                getEditor: function () {
                    return new Ext.form.NumberField({
                        minValue: 0,
                        maxValue: 100
                    });
                }.bind(this),
                renderer: function (value, cell, record) {
                    var prefix = '';
                    if (record.get('pricingBehaviour') === 'percentage_increase') {
                        prefix = '+';
                    } else if (record.get('pricingBehaviour') === 'percentage_discount') {
                        prefix = '-';
                    }

                    cell.tdStyle = value === 0 ? 'color: grey; font-style: italic;' : '';
                    if (value !== undefined) {
                        return prefix + value + '%';
                    }
                    return '--';
                }
            },
        ];
    },

    beforeEdit: function(record, editor, context) {
        if (context.column.name === 'tier_percentage') {
            if (['percentage_decrease', 'percentage_increase'].indexOf(record.get('pricingBehaviour')) === -1) {
                return false;
            }
        }
    },

    storeChange: function(model, field) {
        if (field.getValue() !== 'percentage_increase' && field.getValue() !== 'percentage_decrease') {
            model.set('percentage', 0);
        }
    },

    defaultValues: function(lastEntry) {
        return {
            percentage: 0
        };
    },

    adjustRangeStoreData: function(entry) {
        return entry;
    },

    getData: function(record) {
        return {
            'percentage': record.get('percentage')
        };
    },
});
