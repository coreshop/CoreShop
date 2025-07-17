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
