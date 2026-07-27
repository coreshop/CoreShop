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

pimcore.registerNS('coreshop.shippingrule.conditions.products');
coreshop.shippingrule.conditions.products = Class.create(coreshop.rules.conditions.abstract, {

    type: 'products',
    products: null,

    getForm: function () {
        this.products = new coreshop.object.objectMultihref(this.data ? this.data.products : [], {
            classes: this.getFormattedStackClasses(coreshop.stack.coreshop.product),
            name: 'products',
            title: '',
            height: 200,
            width: 500,
            columns: [],

            columnType: null,
            datatype: 'data',
            fieldtype: 'objects'
        });

        this.includeVariants = Ext.create({
            xtype: 'checkbox',
            fieldLabel: t('coreshop_condition_include_variants'),
            name: 'include_variants',
            checked: this.data ? this.data.include_variants : false
        });


        this.form = new Ext.form.Panel({
            items: [
                this.products.getLayoutEdit(),
                this.includeVariants
            ]
        });

        return this.form;
    },

    getValues: function () {
        return {
            products: this.products.getValue(),
            include_variants: this.includeVariants.getValue()
        };
    }
});
