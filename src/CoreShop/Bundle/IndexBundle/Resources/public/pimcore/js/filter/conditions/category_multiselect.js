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

pimcore.registerNS('coreshop.filter.conditions.category_multiselect');

coreshop.filter.conditions.category_multiselect = Class.create(coreshop.filter.conditions.abstract, {
    type: 'category_multiselect',

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

        this.preSelects = this.data.configuration.preSelects;

        this.categoryMultiSelect = new coreshop.object.objectMultihref(this.preSelects ? this.preSelects : [], {
            classes: this.getFormattedStackClasses(coreshop.stack.coreshop.category),
            name: 'preSelects',
            title: 'preSelects',
            height: 200,
            width: 500,
            columns: [],
            columnType: null,
            datatype: 'data',
            fieldtype: 'objects',
        });

        this.includeSubCategories = Ext.create({
            xtype: 'checkbox',
            fieldLabel: t('coreshop_filters_include_subcategories'),
            name: 'includeSubCategories',
            checked: this.data.configuration.includeSubCategories
        });

        var concatenators = Ext.create('Ext.data.Store', {
            fields: ['value', 'name'],
            data : [
                {"value":"OR", "name":"OR"},
                {"value":"AND", "name":"AND"}
            ]
        });

        this.concatenator = Ext.create('Ext.form.ComboBox', {
            fieldLabel: 'Choose concatenator',
            store: concatenators,
            queryMode: 'local',
            displayField: 'name',
            valueField: 'value',
            value: this.data.configuration.concatenator ? this.data.configuration.concatenator : concatenators.first(),
            renderTo: Ext.getBody()
        });

        return [
            this.categoryMultiSelect.getLayoutEdit(),
            this.includeSubCategories,
            this.concatenator
        ];
    },

    getFormattedStackClasses: function (stackClasses) {
        var classes = [];
        if (Ext.isArray(stackClasses)) {
            Ext.Array.each(stackClasses, function (cClass) {
                classes.push({classes: cClass});
            });
        }
        return classes;
    },

    getFormValues: function () {
        return {
            includeSubCategories: this.includeSubCategories.getValue(),
            preSelects: this.categoryMultiSelect.getValue(),
            concatenator: this.concatenator.getValue(),
        }
    }
});
