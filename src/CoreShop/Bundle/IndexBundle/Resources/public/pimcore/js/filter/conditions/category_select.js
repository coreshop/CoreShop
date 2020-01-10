/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.filter.conditions.category_select');

coreshop.filter.conditions.category_select = Class.create(coreshop.filter.conditions.abstract, {
    type: 'category_select',

    getDefaultItems: function () {
        return [
            {
                xtype: 'textfield',
                name: 'label',
                width: 400,
                fieldLabel: t('label'),
                value: this.data.label
            }
        ];
    },

    getItems: function () {

        var catValue = this.data.configuration.preSelect;
        var categorySelect = new coreshop.object.elementHref({
            id: catValue,
            type: 'object',
            subtype: coreshop.class_map.coreshop.category
        }, {
            objectsAllowed: true,
            classes: [{
                classes: coreshop.class_map.coreshop.category
            }],
            name: 'preSelect',
            title: t('coreshop_filters_category_name')
        });

        return [
            categorySelect.getLayoutEdit(),
            {
                xtype: 'checkbox',
                fieldLabel: t('coreshop_filters_include_subcategories'),
                name: 'includeSubCategories',
                checked: this.data.configuration.includeSubCategories
            }
        ];
    }
});
