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

pimcore.registerNS('coreshop.shippingrule.conditions.categories');
coreshop.shippingrule.conditions.categories = Class.create(coreshop.rules.conditions.abstract, {

    type: 'categories',
    categories: null,

    getForm: function () {
        this.categories = new coreshop.object.objectMultihref(this.data ? this.data.categories : [], {
            classes: this.getFormattedStackClasses(coreshop.stack.coreshop.category),
            name: 'categories',
            title: '',
            height: 200,
            width: 500,
            columns: [],

            columnType: null,
            datatype: 'data',
            fieldtype: 'objects'
        });

        this.recursive = Ext.create({
            xtype: 'checkbox',
            fieldLabel: t('coreshop_condition_recursive'),
            name: 'recursive',
            checked: this.data ? this.data.recursive : false
        });

        this.form = new Ext.form.Panel({
            items: [
                this.categories.getLayoutEdit(),
                this.recursive
            ]
        });

        return this.form;
    },

    getValues: function () {
        return {
            categories: this.categories.getValue(),
            recursive: this.recursive.getValue()
        };
    }
});
